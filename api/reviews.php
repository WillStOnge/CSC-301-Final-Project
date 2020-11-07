<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../objects/utils/database.php";

session_start();

if (!isset($_GET['worker_id']))
{
    http_response_code(400);
    die('[]');
}

if (!isset($_SESSION['user_id']))
{
    echo json_encode(['message' => 'You must be logged in to use this page.']);
    http_response_code(400);
    die('[]');
}

if ($_SERVER["REQUEST_METHOD"] == "GET") 
{
    $db = new Database();
    $reviewInfo;
    $reviewIds = array();

    try
    {
        $db->conn->beginTransaction();
        $stmt = $db->conn->prepare("SELECT name, star_rating, description, create_date FROM review NATURAL JOIN user WHERE worker_id = :worker_id");
        $stmt->bindParam(":worker_id", $_GET['worker_id'], PDO::PARAM_INT);
        $stmt->execute();
        $reviewInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($reviewInfo === false)
            die('[]');

        $db->conn->commit();
    }
    catch (Exception $e)
    {
        $db->conn->rollBack();
        http_response_code(500);
        die('[]');
    }

    foreach ($reviewInfo as &$review)
        array_push($reviewIds, array(
            "name" => $review["name"],
            "star_rating" => $review["star_rating"],
            "description" => $review["description"],
            "create_date" => $review["create_date"])
        );
    
    echo json_encode($reviewIds);
}
?>