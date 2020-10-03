<?php
include_once "utils/iobject.php";

class Tag extends IObject
{
    public $tag_id;
    public $worker_id;
    public $tag_name;
    
    static function create($worker_id, $tag_name)
    {
        $tag = new Tag();

        $tag_id = 0;
        try
        {
            $tag->conn->beginTransaction();
            $stmt = $tag->conn->prepare("INSERT INTO tag (worker_id, tag_name) VALUES (:worker_id, :tag_name)");
            $stmt->bindParam(':worker_id', $worker_id, PDO::PARAM_INT);
            $stmt->bindParam(':tag_name', $tag_name);
            $stmt->execute();

            $tag_id = $tag->conn->lastInsertId(); 

            $tag->conn->commit();
        }
        catch (Exception $e)
        {
            $tag->conn->rollBack();
            http_response_code(500);
            die('Error 500');
        }

        return $tag->read($tag_id);
    }

    static function read($tag_id)
    {
        $record = NULL;
        $tag = new Tag();

        try
        {
            $tag->conn->beginTransaction();
            $stmt = $tag->conn->prepare("SELECT * FROM tag WHERE tag_id = :tag_id");
            $stmt->bindParam(":tag_id", $tag_id, PDO::PARAM_INT);
            $stmt->execute();
            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($record === false)
                throw new Exception("Record not found.");

            $tag->conn->commit();
        }
        catch (Exception $e)
        {
            $tag->conn->rollBack();
            http_response_code(500);
            die('Error 500');
        }

        $tag->tag_id = $record["tag_id"];
        $tag->worker_id = $record["worker_id"];
        $tag->tag_name = $record["tag_name"];

        return $tag;
    }

    function update()
    {
        try
        {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("UPDATE tag SET worker_id = :worker_id, tag_name = :tag_name WHERE tag_id = :tag_id");
            $stmt->bindParam(':tag_id', $this->tag_id, PDO::PARAM_INT);
            $stmt->bindParam(':worker_id', $this->worker_id, PDO::PARAM_INT);
            $stmt->bindParam(':tag_name', $this->tag_name);
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

    function delete()
    {
        try
        {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("DELETE FROM tag WHERE tag_id = :tag_id");
            $stmt->bindParam(':tag_id', $this->tag_id, PDO::PARAM_INT);
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