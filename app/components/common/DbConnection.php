<?php

namespace app\components\common;

use PDO;

/**
 * Class DbConnection
 * @package app\components\common
 */
class DbConnection {
    /**
     * @var \PDO
     */
    private $db;

    /**
     * Taking te object of PDO
     * DbConnection constructor.
     */
    public function __construct() {
        $user_db = require __DIR__.'/../../../config/db.php';
        try {
            $this->db = new PDO("mysql:host = $user_db[0]; dbname = $user_db[1]", $user_db[2], $user_db[3]);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo $e->getMessage();
            echo $e->getTraceAsString();
        }
    }

    /**
     * Adding users to base
     * @param $id
     * @param $subscribing
     * @return void
     */
    public function pushToBase($id, $subscribing) {
        $sql = "INSERT INTO `freelansim-bot`.users(chat_id,subscribe) VALUES ($id,$subscribing)";
        try {
            $this->db->exec($sql);
        } catch (\PDOException $e) {
            echo $e->getMessage();
            echo $e->getTraceAsString();
        } finally {
            $this->db = null;
        }
    }

    /**
     * Return numeric chat_id of users from base
     * @return mixed
     */
    public function getUsers() {
        $sql = "SELECT chat_id FROM `freelansim-bot`.users WHERE subscribe = 1";
        try{
            $state = $this->db->query($sql);
            $result = $state->fetchAll(PDO::FETCH_NUM);
        } catch (\PDOException $e) {
            echo $e->getMessage();
            echo $e->getTraceAsString();
        } finally {
            $this->db = null;
        }
        return $result;
    }
}