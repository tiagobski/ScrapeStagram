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

    public function get($factor_value = false)
    {
      $queryInfo = [
        'query' => 'SELECT * FROM posts WHERE external_id_posts = ?',
        'parameters' => array('i', $factor_value),
        'close' => FALSE
      ];

      $queryResult = $this->db->query($queryInfo['query'], $queryInfo['parameters'], $queryInfo['close']);
      return $queryResult;
    }

    public function insert($account_id, $location_id, $post_external_id, $post_external_url, $post_shortcode, $post_description)
    {
      $queryInfo = [
        'query' => 'INSERT INTO posts (ref_accounts_posts, ref_locations_posts, external_id_posts, external_url_posts, shortcode_posts, description_posts) VALUES (?, ?, ?, ?, ?, ?)',
        'parameters' => array('iiisss', $account_id, $location_id, $post_external_id, $post_external_url, $post_shortcode, $post_description),
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