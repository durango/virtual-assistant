<?php
// PEAR standard
error_reporting(E_ALL | E_STRICT);

// Load Config
require_once __DIR__.'/config.php';

// Load Smarty
require_once __DIR__.'/lib/Smarty/Smarty.class.php';

class Core extends Config {
    // Setup the main variables...
    public $routes;
    public $smarty;
    // Set a default index page...
    public $default_index = 'index';

    /**
     * Initializes the Core class
     *
     * @returns void
     * @access public
     */
    function __construct()
    {
        // Start the session
        session_start();

        // Grab the config's options
        parent::__construct();

        // DRY: Directory where the main files are (controllers)
        $this->routes = __DIR__.'/routes/';

        // Set the default path if it doesn't exist
        if (empty($_GET['path'])) {
            $_GET['path'] = $this->default_index;
        }

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
          require_once $this->routes.'welcome.php';
          break;

      default:
          require_once $this->routes.'index.php';
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
