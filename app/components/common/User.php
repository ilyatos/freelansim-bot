<?php

namespace app\components\common;

use PDO;

/**
 * Class DbConnection
 *
 * @package app\components\common
 */
class User {

    /**
     * @var \PDO
     */
    private $db;

    /**
     * Adding or updating users in base.
     *
     * @param $id
     * @param $subscribing
     */
    public function pushToBase($id, $subscribing)
    {
        $sql = "INSERT INTO `freelansim-bot`.users(chat_id,subscribe) VALUES ($id,$subscribing) ON DUPLICATE KEY UPDATE `subscribe`=$subscribing";
        try {
            $this->openConnection();
            $this->db->exec($sql);
        } catch (\PDOException $e) {
            echo $e->getMessage();
            echo $e->getTraceAsString();
        } finally {
            $this->closeConnection();
        }
    }

    /**
     * Return numeric chat_id of users from base.
     *
     * @return mixed
     */
    public function getUsers() {
        $sql = "SELECT chat_id FROM `freelansim-bot`.users WHERE subscribe = 1";
        try{
            $this->openConnection();
            $state = $this->db->query($sql);
            $result = $state->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            echo $e->getMessage();
            echo $e->getTraceAsString();
        } finally {
            $this->closeConnection();
        }
        return $result;
    }

    /**
     * Open connection to the database.
     */
    private function openConnection()
    {
        $userDb = require __DIR__.'/../../../config/db.php';
        $this->db = new PDO("mysql:host = $userDb[0]; dbname = $userDb[1]", $userDb[2], $userDb[3]);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Close connection at all.
     */
    private function closeConnection()
    {
        $this->db = null;
    }
}