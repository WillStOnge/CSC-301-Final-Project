<?php
class Database
{   
    public $conn = null;

    function __construct()
    {
        include_once "config.php";

        try
        {
            $this->conn = new PDO("mysql:host=" . Config::$host . ";dbname=" . Config::$name, Config::$username);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->conn->exec("set names utf8");
        }
        catch(PDOException $e)
        {
            die('Error connecting to database');
        }
    }
}
?>