<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

// {{{ Factory

/**
 * An agnostic way of talking to MySQL, LDAP, or a plain dataset.
 *
 * An agnostic way of talking to MySQL, LDAP, or a plain dataset.
 * It allows you to extend to other database types as well.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Factory
 * @package    Factory
 * @author     Daniel Durante <officedebo@gmail.com>
 * @copyright  None (see license)
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    1.0
 */
class Factory
{
    // {{{ properties

    /**
     * The host to connect to
     *
     * Potential values are '127.0.0.1', 'localhost', or 'ldap.domain.com'
     *
     * @var string
     */
    var $host = '';

    /**
     * The username to use for the connection (not required).
     *
     * Potential values are 'db_usernam', 'user', or 'username'
     *
     * @var string
     */
    var $username = '';

    /**
     * The password to use for the connection (not required).
     *
     * Potential values are 'password', 'pass', '123'
     *
     * @var string
     */
    var $password = '';

    /**
     * The database name to connect to.
     * Potential values for databases are 'db', 'dbname', 'test'
     * Potential values for LDAP are 'dc=domain,dc=com', 'dc=hello,dc=world', etc.
     *
     * @var string
     */
    var $database = '';

    // }}}

    // {{{ __construct()

    /**
     * Initializes the Factory class
     *
     * @param string $data_type Which factory to conect to.
     *                          currently accepts "mysql", "ldap" or "plain"
     *
     * @return void
     *
     * @throws FactoryException When an invalid $data_type was entered.
     *
     * @access public
     */
    function __construct($data_type)
    {
        swtich (strtolower($data_type)) {
        case 'mysql':
            // Include the MySQL factory
            require_once dirname(__FILE__).'/mysql.php';
            $this->conn = new MySQL();
            break;

        case 'ldap':
            // Include the LDAP factory
            require_once dirname(__FILE__).'/ldap.php';
            $this->conn = new LDAP();
            break;

        case 'plain':
            // Include the plain factory
            require_once dirname(__FILE__).'/plain.php';
            $this->conn = new Plain();
            break;

        default:
            throw new FactoryException(
              'Invalid data type for Factory. MySQL, LDAP, or plain accepted.'
            );
            break;
        }
    }

    // }}}

    // {{{ connect()

    /**
     * Connects to the Factory's dataset (LDAP, MySQL, etc).
     *
     * @return void
     * @access public
     */
    public function connect()
    {
        $this->conn->connect($this->host, $this->username, $this->password, $this->database);
    }

    // }}}

    // {{{ select()

    /**
     * Searches within the Factory adapter
     *
     * @param string       $query  The query for selecting the data.
     * @param string       $filter Required for LDAP, but not for any other factories.
     * @param string|array $reduce Required for LDAP, but not for any other factories.
     *
     * @return resource|array The resource/array given from the adapter.
     * @access public
     */
    public function select($query, $filter = '', $reduce = '')
    {
      return $this->conn->select($query, $filter, $reduce);
    }

    // }}}

    // {{{ entries()

    /**
     * Retrieves an array of data that meets the search criteria.
     *
     * @param resource|array $search_resource The resource/array given back
     *                                        from the select function.
     *
     * @return array The array given back from the search criteria.
     * @access public
     */
    public function entries($search_resource)
    {
        return $this->conn->entries($search_resource);
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
?>