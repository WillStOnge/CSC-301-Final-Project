<?php
include_once "utils/iobject.php";

class Worker extends IObject
{
    public $worker_id;
    public $user_id;
    public $avatar_path;
    public $description;
    public $location;

    static function create($user_id, $avatar_path, $description, $location)
    {
        $worker = new Worker();

        $worker_id = 0;
        try
        {
            $worker->conn->beginTransaction();
            $stmt = $worker->conn->prepare("INSERT INTO worker (user_id, avatar_path, description, location) VALUES (:user_id, :avatar_path, :description, :location)");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':avatar_path', $avatar_path);
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
            die('Error 500');
        }

        $worker->worker_id = $record["worker_id"];
        $worker->user_id = $record["user_id"];
        $worker->avatar_path = $record["avatar_path"];
        $worker->description = $record["description"];
        $worker->location = $record["location"];

        return $worker;
    }

    function update()
    {
        try
        {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("UPDATE worker SET user_id = :user_id, avatar_path = :avatar_path, description = :description, location = :location WHERE worker_id = :worker_id");
            $stmt->bindParam(':worker_id', $this->worker_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindParam(':avatar_path', $this->avatar_path);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':location', $this->location);

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