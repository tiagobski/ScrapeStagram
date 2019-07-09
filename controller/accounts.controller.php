<?php

require_once('database.controller.php');

class AccountsController
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

    public function get($factor_type = false, $factor_value = false)
    {
      switch ($factor_type)
      {
        case 'external_id':
          $queryInfo = [
            'query' => 'SELECT * FROM accounts WHERE external_id_accounts = ?',
            'parameters' => array('i', $factor_value),
            'close' => FALSE
          ];
          break;
        case 'username':
          $queryInfo = [
            'query' => 'SELECT * FROM accounts WHERE username_accounts = ?',
            'parameters' => array('s', $factor_value),
            'close' => FALSE
          ];
          break;
        default:
          $queryInfo = [
            'query' => 'SELECT * FROM accounts',
            'parameters' => NULL,
            'close' => FALSE
          ];
          break;
      }

      // Query and return
      $queryResult = $this->db->query($queryInfo['query'], $queryInfo['parameters'], $queryInfo['close']);
      return $queryResult;
    }

    public function insert($username, $external_id, $external_url, $biography, $profile_pic, $count_followers, $count_following)
    {
      $queryInfo = [
        'query' => 'INSERT INTO accounts (username_accounts, external_id_accounts, external_url_accounts, biography_accounts, profile_pic_accounts, count_followers_accounts, count_following_accounts) VALUES (?, ?, ?, ?, ?, ?, ?)',
        'parameters' => array('sisssii', $username, $external_id, $external_url, $biography, $profile_pic, $count_followers, $count_following),
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