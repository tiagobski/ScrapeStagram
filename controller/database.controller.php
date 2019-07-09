<?php

require_once(dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'config.php' );

class DatabaseController
{
    private $servername;
    private $username;
    private $password;
    private $dbname;
    public $conn;

    private static $_instance;

    public static function getInstance()
    {
        // https://gist.github.com/skhani/5aebd11015881fb3d288

        // If no instance then make one
        if (!self::$_instance)
            self::$_instance = new self();

        return self::$_instance;
    }

    public function __construct()
    {
        // Configure
        global $config;
        $this->servername = $config['database']['host'];
        $this->username = $config['database']['user'];
        $this->password = $config['database']['password'];
        $this->dbname = $config['database']['name'];

        // Establish connection
        $this->conn = $this->connect();
    }

    public function connect()
    {
        // Connection string
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        // Connection settings
        $this->conn->set_charset('utf8');
        date_default_timezone_set('Europe/Lisbon');

        return $this->conn;
    }

    public function disconnect() {
        // TODO
        throw new Exception('Not developed.');
    }

    private function refValues($arr)
    {
        // Called by function 'execSQL'

        if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
        {
            $refs = array();
            foreach($arr as $key => $value)
                $refs[$key] = &$arr[$key];
            return $refs;
        }
        return $arr;
    }

    public function query($sql, $params, $close)
    {
        // Based on PHP.NET answer: http://php.net/manual/en/mysqli-stmt.bind-param.php#100879

        // Connection
        $mysqli = $this->conn;

        // Reopen connection if it is dead
        if ( !is_resource($mysqli) || !$mysqli->ping() ) {
            self::connect();
        }

        // Prepare query statement
        $stmt = $mysqli->prepare($sql) or die($mysqli->error);

        // Bind parameters if there are any
        if ( !is_null($params) && is_array($params) ) {
            call_user_func_array(array($stmt, 'bind_param'), $this->refValues($params));
        }

        // Execute
        $stmt->execute();

        // Error logging
        if ( !is_null($stmt->error) )
            error_log(__FILE__ . ' | ' . $stmt->error);

        // Prepare data to return
        if ($close) {
            if ( strpos($sql, 'INSERT') !== FALSE ) {
                // Return id of inserted row (INSERT)
                $result = $mysqli->insert_id;
            } else {
                // Returned number of affected rows (UPDATE, DELETE)
                $result = $mysqli->affected_rows;
            }
        } else {
            // Return collided data (SELECT)
            $meta = $stmt->result_metadata();

            while ( $field = $meta->fetch_field() ) {
                $parameters[] = &$row[$field->name];
            }

            call_user_func_array(array($stmt, 'bind_result'), $this->refValues($parameters));

            $results = NULL;
            while ( $stmt->fetch() ) {
                $x = array();
                foreach( $row as $key => $val ) {
                    $x[$key] = $val;
                }
                $results[] = $x;
            }

            $result = $results;
        }

        // Close connection
        $stmt->close();
        $mysqli->close();

        // Return results
        return $result;
    }
}

?>