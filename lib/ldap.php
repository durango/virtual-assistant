<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

// {{{ LDAP

/**
 * Connect to LDAP server
 *
 * Factory for connecting to the LDAP server
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   LDAP
 * @package    LDAP
 * @author     Daniel Durante <officedebo@gmail.com>
 * @copyright  None (see license)
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    1.0
 */

class LDAP
{
    // {{{ Properties

    /**
     * Setup a connection and hold it in a variable
     *
     * @var string
     */
    private $conn;

    /**
     * Bind to the server and hold it in a variable
     *
     * @var resource
     */
    private $bind;

    // }}}

    // {{{ connect()

    /**
     * Connects to LDAP
     *
     * @param string $host     The hostname to connect to
     * @param string $username Required for the Factory but not LDAP
     * @param string $password Required for the Factory but not LDAP
     * @param string $dbname   Required for the Factory but not LDAP
     * 
     * @return resource The LDAP connection resource
     *
     * @throws LDAP_Connection_Exception when it can't connect to
     * the LDAP server.
     *
     * @throws LDAP_Bind_Exception when it can't bind to the LDAP server.
     *
     * @access public
     */
    public function connect($host, $username = '', $password = '', $dbname = '')
    {
        $this->conn = ldap_connect($host);
        if ($this->conn === FALSE) throw new LDAP_Connection_Exception(
            'Unable to connect to the LDAP server.'
        );

        ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, 3); 
        ldap_set_option($this->conn, LDAP_OPT_REFERRALS,        0);

        $this->bind = ldap_bind($this->conn);
        if ($this->bind === FALSE) throw new LDAP_Bind_Exception(
            'Unable to bind to the LDAP server.'
        );

        return $this->conn;
    }

    // }}}

    // {{{ close()

    /**
     * Closes LDAP bind
     *
     * @return bool Status of closing the bind.
     * @access public
     */
    public function close()
    {
        return ldap_unbind($this->conn);
    }

    // }}}

    // {{{ search()

    /**
     * Searches within LDAP with a set criteria
     *
     * @param string $dn     The base DN for the directory.
     * @param string $filter The search filter can be simple or advanced,
                             using boolean operators in the format described
                             in the LDAP documentation
     * @param array $reduce  An array of the required attributes, e.g.
                             array("mail", "sn", "cn"). Note that the "dn"
                             is always returned irrespective of which attributes
                             types are requested
     *
     * @return array The LDAP search resource
     * @access public
     */
    function search($dn, $filter = '', $reduce = array("cn", "givenname", "mail"))
    {
        return ldap_search($this->conn, $dn, $filter, $reduce);
    }

    // }}}

    // {{{ entries()

    /**
     * Retrieves entries within LDAP
     *
     * @param string $search_resource The search resource
     *
     * @return array The LDAP entries
     * @access public
     */
    function entries($search_resource)
    {
        $info = ldap_get_entries($this->conn, $search);
        // Store the results in an array and change the keynames to reflect mysql.
        $array = array();
        for ($i=0; $i < $info['count']; $i++)
        {
            $array[$i]['id']    = $info[$i]['cn'][0];
            $array[$i]['name']  = $info[$i]['cn'][0];
            $array[$i]['email'] = $info[$i]['mail'][0];
        }

        return $array;
    }

    // }}}

    // {{{ log()

    /**
     * Empty log function for Factory
     * @param $guestName Guest's name
     * @param string $employeeName Employee's name
     * @param string $topic        Message
     *
     * @return void
     */
    public function log($guestName, $employeeName, $topic)
    {
    }

    // }}}
}

// }}}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */