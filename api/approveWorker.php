<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../objects/worker.php";
include_once "../objects/user.php";

session_start();

if (!isset($_GET['worker_id']))
{
    http_response_code(400);
    die();
}

if (!isset($_SESSION['user_id']))
{
    echo json_encode(['message' => 'You must be logged in as an admin to use this page.']);
    http_response_code(401);
    die();
}
else
{
    $user = User::read($_SESSION['user_id']);
    if (!$user->is_admin)
    {
        echo json_encode(['message' => 'You must be logged in as an admin to use this page.']);
        http_response_code(402);
        die();
    }
        
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $worker = Worker::read($_GET['worker_id']);
    $db = new Database();

    try
    {
        $db->conn->beginTransaction();
        $stmt = $db->conn->prepare("UPDATE worker SET status = 'APPROVED' WHERE worker_id = :worker_id");
        $stmt->bindParam(':worker_id', $worker->worker_id, PDO::PARAM_INT);
        $stmt->execute();
        $db->conn->commit();

        $db->conn->beginTransaction();
        $stmt = $db->conn->prepare("UPDATE user SET type = 'WORKER' WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $worker->user_id, PDO::PARAM_INT);
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