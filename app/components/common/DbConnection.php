<?php

namespace app\components;


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

    function __construct() {
        try {
            $user_db = require '../../../config/db.php';
            $this->db = new \PDO("mysql:host = $user_db[0];dbname = $user_db[1]", $user_db[2], $user_db[3]);
        } catch (\PDOException $exception) {
            $exception->getTrace();
        }
    }

    /**
     * Adding users to base
     * @param $id
     * @param $subscribing
     * @return void
     */
    function pushToBase($id, $subscribing) {
        $sql = "INSERT INTO users(chat_id, subscribe) VALUES ($id, $subscribing)";
        try {
            $this->db->exec($sql);
        } catch (\PDOException $exception) {
            $exception->getTrace();
        } finally {
            $this->db = null;
        }
    }

    /**
     * Return numeric chat_id of users from base
     * @return mixed
     */

    function getUsers() {
        $sql = "SELECT chat_id FROM users WHERE subscribe = 1";
        try{
            $state = $this->db->query($sql);
            $result = $state->FETCHALL(PDO::FETCH_NUM);  //PDO::FETCH_NUM determines type of returning array
            return $result;
        } catch (\PDOException $e) {
            $e->getTrace();
        } finally {
            $this->db = null;
        }
    }

}

//