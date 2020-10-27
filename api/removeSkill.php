<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../objects/utils/database.php";

session_start();

if (!isset($_GET['worker_id']) || !isset($_GET['skill']))
{
    http_response_code(400);
    die();
}

if (!isset($_SESSION['worker_id']))
{
    echo json_encode(['message' => 'You must be logged in as a worker to use this page.']);
    http_response_code(401);
    die();
}
else
{
    if ($_SESSION['worker_id'] != $_GET['worker_id'])
    {
        echo json_encode(['message' => 'You must be logged in as a worker to use this page.']);
        http_response_code(402);
        die();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $db = new Database();

    try
    {
        $db->conn->beginTransaction();
        $stmt = $db->conn->prepare("DELETE FROM skill WHERE worker_id = :worker_id AND skill_name = :skill_name");
        $stmt->bindParam(':worker_id', $_GET['worker_id'], PDO::PARAM_INT);
        $stmt->bindParam(':skill_name', $_GET['skill']);
        $stmt->execute();
        $db->conn->commit();
    }
    catch (Exception $e)
    {
        $db->conn->rollBack();
        http_response_code(500);
        die('Error 500');
    }
}
?>