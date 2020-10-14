<?php
include_once "utils/iobject.php";

class Worker extends IObject
{
    public $worker_id;
    public $user_id;
    public $avatar_name;
    public $description;
    public $location;
    public $status;

    static function create($user_id, $avatar_name, $description, $location)
    {
        $worker = new Worker();

        $worker_id = 0;
        try
        {
            $worker->conn->beginTransaction();
            $stmt = $worker->conn->prepare("INSERT INTO worker (user_id, avatar_name, description, location) VALUES (:user_id, :avatar_name, :description, :location)");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':avatar_name', $avatar_name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':location', $location);
            $stmt->execute();

            $worker_id = $worker->conn->lastInsertId(); 

            $worker->conn->commit();
        }
        catch (Exception $e)
        {
            $worker->conn->rollBack();
            http_response_code(500);
            die('Error 500');
        }

        return $worker->read($worker_id);
    }

    static function read($worker_id)
    {
        $record = NULL;
        $worker = new Worker();

        try
        {
            $worker->conn->beginTransaction();
            $stmt = $worker->conn->prepare("SELECT * FROM worker WHERE worker_id = :worker_id");
            $stmt->bindParam(":worker_id", $worker_id, PDO::PARAM_INT);
            $stmt->execute();
            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($record === false)
                throw new Exception("Record not found.");

            $worker->conn->commit();
        }
        catch (Exception $e)
        {
            $worker->conn->rollBack();
            http_response_code(500);
            die('Unable to find worker with id ' . $worker_id);
        }

        $worker->worker_id = $record["worker_id"];
        $worker->user_id = $record["user_id"];
        $worker->avatar_name = $record["avatar_name"];
        $worker->description = $record["description"];
        $worker->location = $record["location"];
        $worker->status = $record["status"];

        return $worker;
    }

    function update()
    {
        try
        {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("UPDATE worker SET user_id = :user_id, avatar_name = :avatar_name, description = :description, location = :location, status = :status WHERE worker_id = :worker_id");
            $stmt->bindParam(':worker_id', $this->worker_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindParam(':avatar_name', $this->avatar_name);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':location', $this->location);
            $stmt->bindParam(':status', $this->status);

            $this->conn->commit();
        }
        catch (Exception $e)
        {
            $this->conn->rollBack();
            http_response_code(500);
            die('Error 500');
        }
    }

    function delete()
    {
        try
        {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("DELETE FROM worker WHERE worker_id = :worker_id");
            $stmt->bindParam(':worker_id', $this->worker_id, PDO::PARAM_INT);
            $stmt->execute();

            $this->conn->commit();
        }
        catch (Exception $e)
        {
            $this->conn->rollBack();
            http_response_code(500);
            die('Error 500');
        }
    }
}
?>