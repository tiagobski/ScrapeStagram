<?php

require_once('database.controller.php');

class LocationsController
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

    public function get($factor_value = false)
    {
      $queryInfo = [
        'query' => 'SELECT * FROM locations WHERE external_id_locations = ?',
        'parameters' => array('i', $factor_value),
        'close' => FALSE
      ];

      $queryResult = $this->db->query($queryInfo['query'], $queryInfo['parameters'], $queryInfo['close']);
      return $queryResult;
    }

    public function insert($external_id_locations, $slug_locations, $name_locations)
    {
      $queryInfo = [
        'query' => 'INSERT INTO locations (external_id_locations, slug_locations, name_locations) VALUES (?, ?, ?)',
        'parameters' => array('iss', $external_id_locations, $slug_locations, $name_locations),
        'close' => TRUE
      ];

      $queryResult = $this->db->query($queryInfo['query'], $queryInfo['parameters'], $queryInfo['close']);
      return $queryResult;
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