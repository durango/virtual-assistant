<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Custom exceptions for the Virtual Assistant application.
 *
 * Custom exceptions for the Virtual Assistant application.
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

// {{{ MySQL_Connection_Exception

class MySQL_Connection_Exception extends Exception
{
}

// }}}

// {{{ LDAP_Connection_Exception

class LDAP_Connection_Exception extends Exception
{
}

// }}}

// {{{ LDAP_Bind_Exception

class LDAP_Bind_Exception extends Exception
{
}

// }}}
