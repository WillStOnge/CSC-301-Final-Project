<?php
class Database
{
    private $host = "127.0.0.1:3306";
    private $db_name = "final_project";
    private $username = "root";
    
    public $conn = null;

    function __construct()
    {
        try
        {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username);
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