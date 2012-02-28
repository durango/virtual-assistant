<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

// {{{ MySQL

/**
 * Connect to MySQL server
 *
 * Factory for connecting to the MySQL server
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

class MySQL
{
    // {{{ Properties

    /**
     * Setup a connection and hold it in a variable
     *
     * @var string
     */
    private $conn;

    /**
     * Hold the query resource
     *
     * @var resource
     */
    public $query;

    // }}}

    // {{{ connect()

    /**
     * Connects to MySQL
     *
     * @param string $host     The hostname to connect to
     * @param string $username MySQL username
     * @param string $password MySQL password
     * @param string $dbname   MySQL database name
     * 
     * @return resource The MySQL connection resource
     *
     * @throws MySQL_Connection_Exception when it can't connect to
     * the MySQL server.
     *
     * @access public
     */
    public function connect($host, $username = '', $password = '', $dbname = '')
    {
        $this->conn = mysqli_connect($host, $username, $password, $dbname);
        if ($this->conn === FALSE || $this->conn->connect_errno) throw new MySQL_Connection_Exception(
            'Unable to connect to the MySQL server.'
        );

        return $this->conn;
    }

    // }}}

    // {{{ close()

    /**
     * Closes the MySQL resource
     *
     * @return bool Status of closing the MySQL connection.
     * @access public
     */
    public function close()
    {
        return $this->conn->close();
    }

    // }}}

    // {{{ search()

    /**
     * Searches within MySQL with a set criteria
     *
     * @param string $query  The query to select your employees, data, etc.
     * @param string $filter Required for LDAP adapter, unecessary for MySQL
     * @param string $reduce Required for LDAP adapter, unecessary for MySQL
     *
     * @return array The MySQL search (query) resource
     * @access public
     */
    function search($query, $filter = '', $reduce = '')
    {
        $this->query = $this->conn->query($query);
        return $this->query;
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
        // Store the values into an array, clear, then return
        $array = array();
        while ($row = $this->query->fetch_object())
        {
            $array[] = $row;
        }

        // Free up the resource
        $this->query->close();
        $this->conn->next_result();

        return $array;
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