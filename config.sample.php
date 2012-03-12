<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Config class for Virtual-Assistant web application.
 *
 * Config class for Virtual-Assistant web application.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Config
 * @package    Config
 * @author     Daniel Durante <officedebo@gmail.com>
 * @copyright  None (see license)
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    1.0
 */

// {{{ Config

class Config
{
    // {{{ Properties

    /**
     * Store all of the config values in an array
     *
     * @var array
     */
    public $config;

    // }}}

    /**
     * Initialize the Config class
     *
     * @returns void
     * @access public
     */
    function __construct()
    {
        // Setup smarty variables
        $this->config['smarty_template_directory'] = __DIR__.'/tmpl';
        $this->config['smarty_cache_directory']    = __DIR__.'/cache';
        $this->config['smarty_compile_directory']  = __DIR__.'/cache/compiled';
  
        // Setup LDAP variables
        $this->config['ldap_host']      = 'ldap://domain.com';
  
        // Setup MySQL variables
        $this->config['mysql_host']     = 'localhost';
        $this->config['mysql_username'] = 'root';
        $this->config['mysql_password'] = '';
        $this->config['mysql_database'] = '';
  
        // Which database scheme do we want to use?
        // Elgiible values: "mysql" or "ldap"
        $this->config['use_schema']     = 'mysql';

        // Setup where the routes are stored.
        $this->config['routes']         = __DIR__.'/routes/';

        // Set the default index
        $this->config['default_index']  = 'index';

        // Jabber information
        $this->config['jabber_user']    = '';
        $this->config['jabber_pass']    = '';
        $this->config['jabber_host']    = '';
        $this->config['jabber_port']    = '';
    }
}

// }}}
