<?php

require_once('database.controller.php');

class PostsController
{
    /* ==================================================================
     * Constructor & Properties
     * ================================================================== */

    private static $_instance;

    public function __construct()
    {
        $this->db = DatabaseController::getInstance();
        $this->conn = $this->db->connect();
    }

    public static function getInstance()
    {
        if (!self::$_instance) { // If no instance then make one
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /* ==================================================================
     * Functions
     * ================================================================== */

    public function get()
    {
		throw new Exception('Not implemented');
    }

    public function insert()
    {
		throw new Exception('Not implemented');
    }

    public function update()
    {
		throw new Exception('Not implemented');
    }

    public function delete()
    {
		throw new Exception('Not implemented');
    }
}

?>