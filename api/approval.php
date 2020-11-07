<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../objects/utils/database.php";
include_once "../objects/user.php";

session_start();

if (!isset($_SESSION['user_id']) || !(isset($_SESSION['is_admin']) && $_SESSION['is_admin']))
{
    echo json_encode(['message' => 'You must be logged in as an admin to use this page.']);
    http_response_code(400);
    die('[]');
}

if ($_SERVER["REQUEST_METHOD"] == "GET") 
{
    $db = new Database();
    $workerInfo;
    $workerIds = array();

    try
    {
        $db->conn->beginTransaction();
        $stmt = $db->conn->prepare("SELECT DISTINCT worker.worker_id, name, file_name FROM worker NATURAL JOIN user NATURAL JOIN certification WHERE worker.status = 'NOT APPROVED'");
        $stmt->execute();
        $workerInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($workerInfo === false)
            throw new Exception("Record not found.");

        $db->conn->commit();
    }
    catch (Exception $e)
    {
        $db->conn->rollBack();
        http_response_code(500);
        die('[]');
    }

    foreach ($workerInfo as &$worker)
        array_push($workerIds, array(
            "worker_id" => $worker["worker_id"],
            "name" => $worker["name"],
            "file_name" => $worker["file_name"])
        );
    
    echo json_encode($workerIds);
}
?>