<?php
include_once "utils/iobject.php";

class Skill extends IObject
{
    public $skill_id;
    public $worker_id;
    public $skill_name;

    static function create($worker_id, $skill_name)
    {
        $skill = new Skill();

        $skill_id = 0;
        try
        {
            $skill->conn->beginTransaction();
            $stmt = $skill->conn->prepare("INSERT INTO skill (worker_id, skill_name) VALUES (:worker_id, :skill_name)");
            $stmt->bindParam(':worker_id', $worker_id, PDO::PARAM_INT);
            $stmt->bindParam(':skill_name', $skill_name);
            $stmt->execute();

            $skill_id = $skill->conn->lastInsertId(); 

            $skill->conn->commit();
        }
        catch (Exception $e)
        {
            $skill->conn->rollBack();
            http_response_code(500);
            die('[]');
        }

        return $skill->read($skill_id);
    }

    static function read($skill_id)
    {
        $record = NULL;
        $skill = new Skill();

        try
        {
            $skill->conn->beginTransaction();
            $stmt = $skill->conn->prepare("SELECT * FROM skill WHERE skill_id = :skill_id");
            $stmt->bindParam(":skill_id", $skill_id, PDO::PARAM_INT);
            $stmt->execute();
            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($record === false)
                throw new Exception("Record not found.");

            $skill->conn->commit();
        }
        catch (Exception $e)
        {
            $skill->conn->rollBack();
            http_response_code(500);
            die('[]');
        }

        $skill->skill_id = $record["skill_id"];
        $skill->worker_id = $record["worker_id"];
        $skill->skill_name = $record["skill_name"];

        return $skill;
    }

    function update()
    {
        try
        {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("UPDATE skill SET worker_id = :worker_id, skill_name = :skill_name WHERE skill_id = :skill_id");
            $stmt->bindParam(':skill_id', $this->skill_id, PDO::PARAM_INT);
            $stmt->bindParam(':worker_id', $this->worker_id, PDO::PARAM_INT);
            $stmt->bindParam(':skill_name', $this->skill_name);
            $stmt->execute();

            $this->conn->commit();
        }
        catch (Exception $e)
        {
            $this->conn->rollBack();
            http_response_code(500);
            die('[]');
        }
    }

    function delete()
    {
        try
        {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("DELETE FROM skill WHERE skill_id = :skill_id");
            $stmt->bindParam(':skill_id', $this->skill_id, PDO::PARAM_INT);
            $stmt->execute();

            $this->conn->commit();
        }
        catch (Exception $e)
        {
            $this->conn->rollBack();
            http_response_code(500);
            die('[]');
        }
    }
}
?>