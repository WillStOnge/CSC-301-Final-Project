<?php
// TODO: Test this on the home page.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../objects/utils/database.php";

session_start();

if (!isset($_GET['query']))
{
    http_response_code(400);
    die('[]');
}

if ($_SERVER["REQUEST_METHOD"] == "GET") 
{
    $queries = explode(" ", $_GET['query']);
    $in = "'" . implode("','", $queries) . "'";

    $db = new Database();
    $workers;
    $workerData = array();

    $query = "SELECT DISTINCT worker.*, user.*, rating FROM worker NATURAL JOIN skill NATURAL JOIN user LEFT OUTER JOIN (SELECT worker_id, AVG(star_rating) AS rating FROM review GROUP BY worker_id) reviews ON worker.worker_id = reviews.worker_id WHERE skill_name IN (" . $in . ") AND status = 'APPROVED' AND banned = false ORDER BY rating DESC";

    try
    {
        $db->conn->beginTransaction();
        $stmt = $db->conn->prepare($query);
        $stmt->execute();
        $workers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($workers === false)
            die('[]');

        $db->conn->commit();
    }
    catch (Exception $e)
    {
        $db->conn->rollBack();
        http_response_code(500);
        die('[]');
    }

    foreach ($workers as &$worker)
    {
        $description = substr($worker["description"], 0, 150);

        $description = strlen($description) < 150 ? $description : rtrim($description) . "...";

        array_push($workerData, array(
            "worker_id" => $worker["worker_id"],
            "description" => $description,
            "location" => $worker["location"],
            "name" => $worker["name"],
            "rating" => $worker["rating"])
        );
    }
    
    echo json_encode($workerData);
}
?>