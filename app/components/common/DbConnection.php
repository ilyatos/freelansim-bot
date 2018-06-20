<?php

namespace app\components\common;

use PDO;

/**
 * Class DbConnection
 *
 * @package app\components\common
 */
class DbConnection {

    /**
     * @var \PDO
     */
    private $db;

    /**
     * Adding or updating users in base.
     *
     * @param $id
     * @param $subscribing
     * @return void
     */
    public function pushToBase($id, $subscribing) {
        $sql = "UPDATE `freelansim-bot`.users SET subscribe = $subscribing WHERE chat_id = $id";
        $condition = "SELECT count(*) FROM `freelansim-bot`.users WHERE chat_id = $id";
        try {
            $this->openConnection();
            $result = $this->db->prepare($condition);
            $result->execute();
            $numberOfRows = $result->fetchColumn();
            if ($numberOfRows > 0) {
                $sql = $this->db->prepare($sql);
                $sql->execute();
            } else {
                $sql = "INSERT INTO `freelansim-bot`.users(chat_id,subscribe) VALUES ($id,$subscribing)";
                $this->db->exec($sql);
            }
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
     * Close connection at all.
     */
    private function closeConnection()
    {
        $this->db = null;
    }
}