<?php
/**
 * Created by PhpStorm.
 * User: broadside13_code
 * Date: 17.06.18
 * Time: 17:58
 */

namespace app;


class db_connection{
    private $db;
    function __construct()
    {
        try {
            $user_db = require 'config/db.php';
            $db = new \PDO("mysql:host = $user_db[host];dbname = $user_db[databse]", $user_db[user], $user_db[password]);
        }catch (\PDOException $exception){$exception->getTrace();}
    }

    function push_to_base($id, $subscribing){
        $sql = "INSERT INTO users(chat_id, subscribe) VALUES ($id, $subscribing)";
        try {
            $this->db->execute($sql);
        }catch (\PDOException $exception){$exception->getTrace();}
    }

    function get_users(){
        $sql = "SELECT chat_id FROM users WHERE subscribe = 1";
        try{
            $state = $this->db->query($sql);
            $result = $state->FETCHALL(PDO::FETCH_NUM);
            return $result;
        }catch (\PDOException $e){$e->getTrace();}

    }

}







    ?>