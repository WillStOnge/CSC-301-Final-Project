<?php

include_once "database.php";

abstract class IObject
{
    protected $conn;

    function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }
}
?>