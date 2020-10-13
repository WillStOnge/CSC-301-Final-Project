<?php
include_once "utils/iobject.php";

class Certification extends IObject
{
    public $certification_id;
    public $worker_id;
    public $file_name;

    static function create($worker_id, $file_name)
    {
        $certification = new Certification();

        $certification_id = 0;
        try
        {
            $certification->conn->beginTransaction();
            $stmt = $certification->conn->prepare("INSERT INTO certification (worker_id, file_name) VALUES (:worker_id, :file_name)");
            $stmt->bindParam(':worker_id', $worker_id, PDO::PARAM_INT);
            $stmt->bindParam(':file_name', $file_name);
            $stmt->execute();

            $certification_id = $certification->conn->lastInsertId(); 

            $certification->conn->commit();
        }
        catch (Exception $e)
        {
            $certification->conn->rollBack();
            http_response_code(500);
            die('Error 500');
        }

        return $certification->read($certification_id);
    }

    static function read($certification_id)
    {
        $record = NULL;
        $certification = new Certification();

        try
        {
            $certification->conn->beginTransaction();
            $stmt = $certification->conn->prepare("SELECT * FROM certification WHERE certification_id = :certification_id");
            $stmt->bindParam(":certification_id", $certification_id, PDO::PARAM_INT);
            $stmt->execute();
            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($record === false)
                throw new Exception("Record not found.");

            $certification->conn->commit();
        }
        catch (Exception $e)
        {
            $certification->conn->rollBack();
            http_response_code(500);
            die('Error 500');
        }

        $certification->certification_id = $record["certification_id"];
        $certification->worker_id = $record["worker_id"];
        $certification->file_name = $record["file_name"];

        return $certification;
    }

    function update()
    {
        try
        {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("UPDATE certification SET worker_id = :worker_id, file_name = :file_name WHERE certification_id = :certification_id");
            $stmt->bindParam(':certification_id', $this->certification_id, PDO::PARAM_INT);
            $stmt->bindParam(':worker_id', $this->worker_id, PDO::PARAM_INT);
            $stmt->bindParam(':file_name', $this->file_name);
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
            $stmt = $this->conn->prepare("DELETE FROM certification WHERE certification_id = :certification_id");
            $stmt->bindParam(':certification_id', $this->certification_id, PDO::PARAM_INT);
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