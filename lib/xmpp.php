<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

// {{{ XMPP

/**
 * A short way to auth against Jabber/XMPP protocol.
 *
 * A short way to auth against Jabber/XMPP protocol.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   XMPP
 * @package    XMPP
 * @author     Daniel Durante <officedebo@gmail.com>
 * @copyright  None (see license)
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    1.0
 */
 
include_once dirname(__DIR__).'/lib/Auth/SASL/DigestMD5.php';
require_once dirname(__DIR__).'/lib/Auth/SASL/CramMD5.php';

class XMPP
{
    // {{{ Properties

    /**
     * Set whether we want ot debug or not
     *
     * @var bool
     */
    public $debug = false;

    /**
     * Jabber username
     *
     * @var string
     */
    public $user = false;

    /**
     * Jabber password
     *
     * @var string
     */
    public $pass = false;

    /**
     * Jabber host/server
     * @var string
     */
    public $host = false;

    /**
     * Jabber server port number
     *
     * @var int
     */
    public $port = false;

    /**
     * Determines whether or not we're streaming
     *
     * @var bool
     * @access private
     */
    private $stream = false;

    /**
     * Returns a stream error number.
     *
     * @var int
     * @access private
     */
    private $streamErrorNum = false;

    /**
     * Stores the stream error into a string.
     *
     * @var string
     * @access private
     */
    private $streamError = false;

    /**
     * Sets the stream timeout.
     *
     * @var int
     * @access private
     */
    private $streamTimeout = 20;

    /**
     * Determines whether we want blocking in the stream
     *
     * @var int
     * @access private
     */
    private $streamBlocking = 0;

    /**
     * Determines whether or not we need to re-stream for authorization
     *
     * @var bool
     * @access private
     */
    private $auth_stream;

    /**
     * Sets our authorization type
     * e.g. DIGEST-MD5, CRAM, PLAIN, etc.
     *
     * @var string
     * @access private
     */
    private $auth_type;

    /**
     * Stores our online users.
     *
     * @var array
     * @access public
     */
    public $online_users;

    /**
     * Stores the DOM object into a variable
     *
     * @var DOMDocument
     * @access private
     */
    private $dom;

    /**
     * Stores an array of XPath namespaces
     *
     * @var array
     * @access private
     */
    private $xpath;

    /**
     * Keeps track of how many ticks we've gone by
     * without receiving anything from the buffer
     *
     * @var int
     * @access private
     */
    private $ticks = 0;

    /**
     * Stores any kind of information to pass onto a hook
     *
     * @var mixed
     * @access public;
     */
    public $special;

    // {{{ __construct()

    /**
     * Initializes the XMPP class
     *
     * @param string $user Jabber username.
     * @param string $pass Jabber password.
     * @param string $host Jabber server/host.
     * @param int    $port Jabber server's port number.
     *
     * @return void
     *
     * @access public
     */
    public function __construct($user, $pass, $host, $port)
    {
        $this->dom  = new DOMDocument();
        $this->user = $user;
        $this->pass = $pass;
        $this->host = $host;
        $this->port = $port;

        $this->xpaths[] = array('tls',     'urn:ietf:params:xml:ns:xmpp-tls');
        $this->xpaths[] = array('stream',  'http://etherx.jabber.org/streams');
        $this->xpaths[] = array('sasl',    'urn:ietf:params:xml:ns:xmpp-sasl');
        $this->xpaths[] = array('bind',    'urn:ietf:params:xml:ns:xmpp-bind');
        $this->xpaths[] = array('session', 'urn:ietf:params:xml:ns:xmpp-session');
        $this->xpaths[] = array('j',       'http://jabber.org/protocol/disco#items');

        register_shutdown_function(array($this, "disconnect"));
    }

    // }}}

    // {{{ start()

    /**
     * Starts the XMPP connection and main loop
     *
     * @param int $timeout How many times do you want to iterate through
                           the stream with nothing returned before closing it?
     *
     * @return void
     *
     * @access public
     */
    public function start($timeout = 5)
    {
        $this->add('connect', array($this, 'streamStart'));
        $this->add('read', array($this, 'read'));

        $start = time();
        if ($this->connect()) {
            while ($this->stream) {
                if ($this->ticks >= $timeout) {
                    $this->disconnect();
                    break;
                }
                $this->read();
            }
        }
    }

    // }}}

    // {{{ authType()

    /**
     * Communicates to the Jabber server about which authorization
     * mechanism we want to use.
     *
     * @param string $type Which mechanism we want to authorize with.
     *
     * @return void
     *
     * @access protected
     */
    protected function authType($type = 'DIGEST-MD5') {
        $this->auth_type = $type;
        switch ($this->auth_type) {
        case 'DIGEST-MD5':
        case 'CRAM-MD5':
            $this->send("<auth xmlns='urn:ietf:params:xml:ns:xmpp-sasl' mechanism='{$type}' />");
            break;

        default:
            $sasl    = new Auth_SASL_Plain();
            $uncoded = $sasl->getResponse($this->user . '@' . $this->host, $this->pass);
            $coded   = base64_encode ($uncoded);

            $this->send("<auth xmlns='urn:ietf:params:xml:ns:xmpp-sasl' mechanism='PLAIN'>{$coded}</auth>");
        break;
        }
    }

    // }}}

    // {{{ authStream()

    /**
     * Initiates a new stream after authorization as required from XMPP protocol
     *
     * @return void
     *
     * @access protected
     */
    protected function authStream()
    {
        $this->auth_stream = true;
        $this->send("<stream:stream xmlns='jabber:client' xmlns:stream='http://etherx.jabber.org/streams' to='{$this->host}' version='1.0'>");
    }

    // }}}

    // {{{ authBind()

    /**
     * Binds to the Jabber stream instance.
     *
     * @return void
     *
     * @access protected
     */
    protected function authBind()
    {
        $this->send('<iq type="set" id="bind-1"><bind xmlns="urn:ietf:params:xml:ns:xmpp-bind"><resource>bot</resource></bind></iq>');
    }

    // }}}

    // {{{ authSession()

    /**
     * Initiates a session bind to the Jabber server.
     *
     * @return void
     *
     * @access protected
     */
    protected function authSession()
    {
        $this->send('<iq type="set" id="session-1"><session xmlns="urn:ietf:params:xml:ns:xmpp-session"/></iq>');
    }

    // }}}

    // {{{ discoServices()

    /**
     * Grabs the discovery services that are available on our server
     *
     * @return void
     *
     * @access protected
     */
    protected function discoServices()
    {
        $this->send("<iq type='get' id='disco-1' to='{$this->host}'><query xmlns='http://jabber.org/protocol/disco#items'/></iq>");
    }

    // }}}

    // {{{ whosOnline()

    /**
     * Asks the Jabber server, "Who's online?"
     *
     * @return void
     *
     * @access protected
     */
    protected function whosOnline()
    {
        $this->send('<iq to="'.$this->host.'" type="get" id="disco-items-1"><query xmlns=\'http://jabber.org/protocol/disco#items\' node="online users"/></iq>');
    }

    // }}}

    // {{{ storeOnlineUsers()

    /**
     * Stores who's online into an array
     *
     * @return array
     *
     * @access protected
     */
    protected function storeOnlineUsers($xml)
    {
        for ($x=0; $x < $xml->length; $x++) {
            $fulljid = $xml->item($x)->getAttribute('jid');
            $jid = explode('/', $fulljid);
            $this->online_users[$jid[0]][] = $fulljid;
        }

        $this->run('send_messages', $this);
    }

    // }}}

    // {{{ authChallenge()

    /**
     * Completes the authorization challenge and sends the response back.
     *
     * @param string $challenge The challenge sent to us by the server.
     *
     * @return void
     *
     * @access protected
     */
    protected function authChallenge($challenge)
    {
        $challenge = base64_decode($challenge);

        $vars = array();
        $matches = array();
        preg_match_all('/(\w+)=(?:"([^"]*)|([^,]*))/', $challenge, $matches);
        $res = array();
        foreach ($matches[1] as $k => $v) {
            $vars[$v] = (empty($matches[2][$k]) ? $matches[3][$k] : $matches[2][$k]);
        }

        switch ($this->auth_type) {
        case 'DIGEST-MD5':
            if (isset($vars['nonce'])) {
                $sasl    = new Auth_SASL_DigestMD5();
                $uncoded = $sasl->getResponse($this->user, $this->pass, $challenge, $this->host, 'xmpp', "{$this->user}@{$this->host}/home");
                $coded   = base64_encode($uncoded);

                $this->send("<response xmlns='urn:ietf:params:xml:ns:xmpp-sasl'>{$coded}</response>");
            } else {
                if (isset($vars['rspauth'])) {
                    // Second step
                    $this->send("<response xmlns='urn:ietf:params:xml:ns:xmpp-sasl'/>");
                } else {
                    $this->log("ERROR receiving challenge : {$challenge}");
                }
            }
            break;

        case 'CRAM-MD5':
            $sasl     = new Auth_SASL_CramMD5();
            $uncoded  = $sasl->getResponse ($this->user, $this->pass, $challenge);
            $coded    = base64_encode ($uncoded);

            $this->send("<response xmlns='urn:ietf:params:xml:ns:xmpp-sasl'>{$coded}</response>");
            break;
        }
    }

    // }}}

    // {{{ connect()

    /**
     * Opens the socket to the Jabber server
     *
     * @return bool
     *
     * @access public
     */
    public function connect()
    {
        if (!$this->stream) {
            if ($this->stream = @fsockopen($this->host, $this->port, $this->streamErrorNum, $this->streamError, $this->streamTimeout)) {
                stream_set_blocking($this->stream, $this->streamBlocking);
                stream_set_timeout($this->stream, $this->streamTimeout);
            } else {
                $this->log("Socket could not be opened for XMPP host {$this->host}:{$this->port}");
            }
        } else {
            $this->log("Socket already opened for xmpp host {$this->host}:{$this->port}");
        }

        $this->run('connect');

        if ($this->stream) {
            return true;
        } else {
            return false;
        }
    }

    // }}}

    // {{{ disconnect()

    /**
     * Disconnects the Jabber server
     *
     * @return void
     *
     * @access public
     */
    public function disconnect() {
        $this->run('shutdown');

        if ($this->stream) {
            $this->log("Shutting down..");
            $this->streamEnd();
        }
        $this->stream = false;
    }

    // }}}

    // {{{ parse()

    /**
     * Parses the XML returned from the server
     *
     * @return void
     *
     * @access public
     */
    public function parse($xml)
    {
        $has_stream     = (strpos(strtolower($xml), 'stream:stream') !==  false);
        $has_end_stream = (strpos(strtolower($xml), '/stream:stream') === false);

        if ($has_stream && $has_end_stream) {
            $xml .= '</stream:stream>';
        }

        $this->dom->loadXML($xml);
        $this->xpath = new DOMXPath($this->dom);

        // Load our XPaths
        if (count($this->xpaths) > 0) {
            foreach ($this->xpaths AS $path) {
                $this->xpath->registerNamespace($path[0], $path[1]);
            }
        }

        // Main authorization functions
        if ($this->auth_stream === true && $this->xpath->query("//bind:bind")->length) {
            $this->auth_stream = false;
            $this->authBind();
        }
        else if ($this->xpath->query("//bind:jid")->length) {
            $this->authSession();
        }
        else if ($this->xpath->query("//sasl:mechanism[contains(normalize-space(.), 'DIGEST-MD5')]")->length) {
            $this->authType();
        }
        else if ($this->xpath->query("//sasl:mechanism[contains(normalize-space(.), 'CRAM-MD5')]")->length) {
            $this->authType('CRAM-MD5');
        }
        else if ($this->xpath->query("//sasl:mechanism[contains(normalize-space(.), 'PLAIN')]")->length) {
            $this->authType('PLAIN');
        }
        else if($this->xpath->query("//sasl:challenge")->length) {
            $this->authChallenge($this->xpath->query("//sasl:challenge")->item(0)->nodeValue);
        }
        else if($this->xpath->query("//sasl:success")->length) {
            $this->authStream();
        }
        // END: Main Authorization functions
        // You may safely edit below this line...
        else if ($this->xpath->query('//j:query[@node="online users"]')->length) {
            $this->storeOnlineUsers($this->xpath->query('//j:item'));
        }
        else if ($this->xpath->query('//j:item[@node="online users"]')->length) {
            $this->whosOnline();
        }
        else if ($this->xpath->query("//session:session")->length) {
            $this->discoServices();
        }
    }

    // }}}

    // {{{ read()

    /**
     * Reads from the socket
     *
     * @return bool|array
     *
     * @access public
     */
    public function read()
    {
        sleep(1);

        $readNothing = 0;
        $data = "";

        while ($readNothing < 10) {
            if ($this->stream) {
                $return = fread($this->stream, 4096);

                if (strlen($return) == 0) {
                    $readNothing++;
                } else {
                    $data .= $return;
                }
            } else {
                break;
            }
        }

        $data = trim(str_replace("<?xml version='1.0'?>", '', $data));
        if (strlen($data) > 0) {
            $this->ticks = 0;
            $this->log("Got {$data}");
            $this->run('data', $data);

            $this->parse($data);

            $xml = array('data' => $this->dom, 'raw' => $data);
            return $xml;
        } else {
            $this->ticks++;
            return false;
        }
    }

    // }}}

    // {{{ send()

    /**
     * Sends data to the socket.
     *
     * @return bool|int
     *
     * @access public
     */
    public function send($data)
    {
        if ($this->stream) {
            if (($return = fwrite($this->stream, $data)) !== false) {
                $this->log("Sent Success: {$data}");
            } else {
                $this->log("Sent Failed: {$data}");
            }
            return $return;
        } else {
            $this->log("Send failed, socket not connected: {$data}");
            return false;
        }
    }

    // }}}

    // {{{ add()

    /**
     * Adds a hook to the XMPP class
     *
     * @param string $hook     The hook's name
     * @param string $callback The function's name to callback on.
     *
     * @return void
     *
     * @access public
     */
    public function add($hook, $callback)
    {
        $this->hooks[$hook][] = $callback;
    }

    // }}}

    // {{{ remove()

    /**
     * Removes a hook from the XMPP class
     *
     * @param string $hook     The hook's name
     * @param string $callback The function's name to callback on.
     *
     * @return void
     *
     * @access public
     */
    public function remove($hook, $callback)
    {
        if (isset($this->hooks[$hook][$callback])) {
            unset($this->hooks[$hook][$callback]);
        }
    }

    // }}}

    // {{{ run()

    /**
     * Removes a hook from the XMPP class
     *
     * @param string $hook   The hook's name
     * @param string $params Send any arguments to the callback
     *
     * @return mixed|bool
     *
     * @access public
     */
    public function run($hook, $params = null) {
        $data = false;
        if (isset($this->hooks[$hook]) && count($this->hooks[$hook]) > 0) {
            foreach ($this->hooks[$hook] as $callback) {
                $data = call_user_func($callback, $params);
            }
        }
        return $data;
    }

    // }}}

    // {{{ streamStart()

    /**
     * Starts the XMPP stream
     *
     * @return bool|int
     *
     * @access public
     */
    public function streamStart() {
        return $this->send("<stream:stream xmlns:stream=\"http://etherx.jabber.org/streams\" version=\"1.0\" xmlns=\"jabber:client\" to=\"{$this->host}\">");
    }

    // }}}

    // {{{ streamEnd()

    /**
     * Stops the XMPP stream
     *
     * @return bool|int
     *
     * @access public
     */
    public function streamEnd() {
        return $this->send("</stream:stream>");
    }

    // }}}

    // {{{ log()

    /**
     * Logs and prints any debug information
     *
     * @param string $message The message to log
     * @param bool   $debug   Change debug mode
     *
     * @return void
     * @access private
     */
    private function log($message, $debug = false) {
        if ($debug || $this->debug) {
            echo("{$message}\n\n");
         }
    }

    // }}}
}

// }}}
?>