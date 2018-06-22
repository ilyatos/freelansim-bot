<?php


namespace app\components\common;

use PDO;

/**
 * Class UserTag
 * @package app\components\common
 */
class UserTag {

    /**
     * @var PDO
     */
    private $db;

    /**
     * Returning all tags for chosen user.
     *
     * @param $chat_id
     * @return array
     */
    function getUserTags($chat_id) {
        try {
            $sql = "SELECT tag FROM `freelansim-bot`.users_tags WHERE chat_id = $chat_id";
            $this->openConnection();
            $state = $this->db->query($sql);
            $result = $state->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e){
            echo $e->getMessage();
            echo $e->getTraceAsString();
        } finally {
            $this->closeConnection();
        }
    }

    /**
     * Insert one tag into base for chosen user if tag not exists.
     *
     * @param $chat_id
     * @param $tag
     * @return string
     */
    function pushTagToBase($chat_id, $tag) {
        $sql = "INSERT INTO `freelansim-bot`.users_tags WHERE chat_id = $chat_id";
        $condition = "SELECT COUNT(*) FROM `freelansim-bot`.users_tags WHERE chat_id = $chat_id AND tag = $tag";
        try {
            $this->openConnection();
            $result = $this->db->prepare($condition);
            $result->execute();
            $numberOfRows = $result->fetchColumn();
            if ($numberOfRows == 0) {
                $sql = $this->db->prepare($sql);
                $sql->execute();
            }
            else {
                return "This tag already exists for this user";
            }
        } catch (\PDOException $e) {
            echo $e->getMessage();
            echo $e->getTraceAsString();
        } finally {
            $this->closeConnection();
        }
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
     * Close connection.
     */
    private function closeConnection()
    {
        $this->db = null;
    }
}