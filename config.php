<?php
// Future MySQL settings, etc. go here...

class Config {
  public $config;

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
      $this->config['ldap_host'] = 'ldap://danieldurante.com';
  }
}
