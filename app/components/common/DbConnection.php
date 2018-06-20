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
     * Adding or updating users in base
     * @param $id
     * @param $subscribing
     * @return void
     */
    public function pushToBase($id, $subscribing) {
        $sql = "UPDATE * `freelansim-bot`.users SET subcribe = $subscribing WHERE chat_id = $id";
        $condition = "SELECT COUNT (*) FROM `freelansim-bot`.users WHERE chat_id = $id";

        try {
            $condition = $this->db->query($condition);
        } catch (\PDOException $e) {
            echo $e->getMessage();
            echo $e->getTraceAsString();
        }

        if ($condition > 0) {
            try {
                $sql = $this->db->prepare($sql);
                $this->db->exec($sql);
            } catch (\PDOException $e){
                echo $e->getMessage();
                echo $e->getTraceAsString();
            } finally {
                $this->db = null;
            }
        }
        else {
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
    }

    /**
     * Return numeric chat_id of users from base
     * @return mixed
     */
    public function getUsers() {
        $sql = "SELECT chat_id FROM `freelansim-bot`.users WHERE subscribe = 1";
        try{
            $state = $this->db->query($sql);
            $result = $state->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            echo $e->getMessage();
            echo $e->getTraceAsString();
        } finally {
            $this->db = null;
        }
        return $result;
    }
}