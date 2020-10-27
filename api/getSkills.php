<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../objects/utils/database.php";
include_once "../objects/user.php";

if (!isset($_GET['worker_id']))
{
    http_response_code(400);
    die();
}

session_start();

if (!isset($_SESSION['user_id']))
{
    echo json_encode(['message' => 'You must be logged in as an admin to use this page.']);
    http_response_code(400);
    die();
}
else
{
    $user = User::read($_SESSION['user_id']);
    if (!$user->is_admin)
    {
        echo json_encode(['message' => 'You must be logged in as an admin to use this page.']);
        http_response_code(400);
        die();
    }
        
}

if ($_SERVER["REQUEST_METHOD"] == "GET") 
{
    $db = new Database();
    $skills;
    $skillNames = array();

    try
    {
        $db->conn->beginTransaction();
        $stmt = $db->conn->prepare("SELECT skill_name FROM skill WHERE worker_id = :worker_id");
        $stmt->bindParam(":worker_id", $_GET['worker_id'], PDO::PARAM_INT);
        $stmt->execute();
        $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($skills === false)
            throw new Exception("Record not found.");

        $db->conn->commit();
    }
    catch (Exception $e)
    {
        $db->conn->rollBack();
        http_response_code(500);
        die('Error 500');
    }

    foreach ($skills as &$skill)
        array_push($skillNames, array(
            "skill_name" => $skill["skill_name"])
        );
    
    echo json_encode($skillNames);
}
?>