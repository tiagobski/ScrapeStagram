<?php

require_once('database.controller.php');

class ImagesController
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
        'query' => 'SELECT * FROM images WHERE external_url_images = ?',
        'parameters' => array('s', $factor_value),
        'close' => FALSE
      ];

      $queryResult = $this->db->query($queryInfo['query'], $queryInfo['parameters'], $queryInfo['close']);
      return $queryResult;
    }

    public function insert($external_url, $width, $height)
    {
      $queryInfo = [
        'query' => 'INSERT INTO images (external_url_images, width_images, height_images) VALUES (?, ?, ?)',
        'parameters' => array('sii', $external_url, $width, $height),
        'close' => TRUE
      ];

      $queryResult = $this->db->query($queryInfo['query'], $queryInfo['parameters'], $queryInfo['close']);
      return $queryResult;
    }

    public function insertPostImageRelation($post_id, $image_id)
    {
      $queryInfo = [
        'query' => 'INSERT INTO posts_has_images (ref_id_posts, ref_id_images) VALUES (?, ?)',
        'parameters' => array('ii', $post_id, $image_id),
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