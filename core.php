<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Core class for Virtual-Assistant web application.
 *
 * Core class for Virtual-Assistant web application.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Core
 * @package    Core
 * @author     Daniel Durante <officedebo@gmail.com>
 * @copyright  None (see license)
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    1.0
 */
 
// PEAR standard
error_reporting(E_ALL | E_STRICT);

// Load Config
require_once __DIR__.'/config.php';

// Load Smarty
require_once __DIR__.'/lib/Smarty/Smarty.class.php';

// Load custom exceptions
require_once __DIR__.'/lib/exceptions.php';

// {{{ Core

class Core extends Config
{
    // {{{ Properties

    /**
     * Store the Smarty resource into a variable.
     *
     * @var resource
     */
    public $smarty;

    /**
     * Store the factory into a variable.
     *
     * @var class
     */
    public $factory;

    /**
     * Store the XMPP class into a variable.
     *
     * @var class
     */
    public $xmpp;

    // }}}

    /**
     * Initializes the Core class
     *
     * @returns void
     * @access public
     */
    function __construct()
    {
        session_start();

        // Grab the config's options
        parent::__construct();

        // Set the default path if it doesn't exist
        if (empty($_GET['path'])) {
            $_GET['path'] = $this->config['default_index'];
        }

        // Initialize the Factory
        require_once __DIR__.'/lib/factory.php';
        $this->factory = new Factory($this->config['use_schema']);

        // Initialize Smarty
        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir($this->config['smarty_template_directory'])
                     ->setCacheDir(   $this->config['smarty_cache_directory'])
                     ->setCompileDir( $this->config['smarty_compile_directory']);

        // Setup the default rack session storage units
        $racks = array('error','notice');

        /*
         If we've set a rack session from the previous page then
         set it for the current page (useful for redirects)
        */
        foreach ($racks AS $storage) {
            // In order to avoid undefine indexes...
            $_SESSION['rack'][$storage] = '';

            if (!empty($_SESSION['x.rack'][$storage])) {
                $_SESSION['rack'][$storage]   = $_SESSION['x.rack'][$storage];
                $_SESSION['x.rack'][$storage] = '';
            }
        }
    }

    /**
     * Stores information that needs to be passed to the next page
     * useful for alerts, notices, etc.
     *
     * @param string $method  Which storage does this rack session belong to?
     *                        legitimate values: error or notice
     * @param string $message The message that you want to store
     *
     * @return void
     * @access public
     */
    public function set_rack($method, $message)
    {
        $_SESSION['x.rack'][$method] = $message;
    }

    /**
     * Redirects the user to a different page.
     *
     * @param string  $url  The URL in which you want to redirect the user.
     * @param boolean $perm Is this redirect permanent?
     *
     * @return void
     * @access public
     */
    public function redirect($url, $perm)
    {
        if ($perm === true) {
            header("HTTP/1.1 301 Moved Permanently");
        }

        header("Location:{$url}");
        exit;
    }

    /**
     * Initiates the routes and load the correct file.
     *
     * @return void
     * @access public
     */
    public function run()
    {
      switch (strtolower($_GET['path'])) {
      case 'welcome':
          require_once $this->config['routes'].'welcome.php';
          break;

      case 'more':
          require_once $this->config['routes'].'more.php';
          break;

      case 'send':
          require_once $this->config['routes'].'send.php';
          break;

      default:
          require_once $this->config['routes'].'index.php';
          break;
      }

      // Clear all of our rack sessions...
      if (!empty($_SESSION['rack'])) {
          if (!empty($_SESSION['rack']['error'])) {
              unset($_SESSION['rack']['error']);
          }
      }
    }
}

// }}}
