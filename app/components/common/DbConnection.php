<?php

namespace app\components\common;


class DbConnection {
    private $db;

    function __construct() {
        try {
            $user_db = require '../../../config/db.php';
            $db = new \PDO("mysql:host = $user_db[host]; dbname = $user_db[database]", $user_db[user], $user_db[password]);
        } catch (\PDOException $exception) {
            $exception->getTrace();
        }
    }

    function pushToBase($id, $subscribing) {
        $sql = "INSERT INTO users(chat_id, subscribe) VALUES ($id, $subscribing)";
        try {
            $this->db->execute($sql);
        } catch (\PDOException $exception) {
            $exception->getTrace();
        } finally {
            $this->db = null;
        }
    }

    function getUsers() {
        $sql = "SELECT chat_id FROM users WHERE subscribe = 1";
        try{
            $state = $this->db->query($sql);
            $result = $state->FETCHALL(PDO::FETCH_NUM);
            return $result;
        } catch (\PDOException $e) {
            $e->getTrace();
        } finally {
            $this->db = null;
        }
    }

}

    ?>