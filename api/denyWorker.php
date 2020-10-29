<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../objects/worker.php";

session_start();

if (!isset($_GET['worker_id']))
{
    http_response_code(400);
    die();
}

if (!isset($_SESSION['user_id']) || !(isset($_SESSION['is_admin']) && $_SESSION['is_admin']))
{
    echo json_encode(['message' => 'You must be logged in as an admin to use this page.']);
    http_response_code(401);
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $worker = Worker::read($_GET['worker_id']);
    $worker->delete();
}
?>