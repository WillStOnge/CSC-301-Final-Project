<?php
abstract class IObject
{
    protected $conn;

    function __construct($db)
    {
        $db = new Database();
        $this->conn = $db->conn;
    }
}
?>