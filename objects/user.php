<?php
class User extends IObject
{
    public $user_id;
    public $name;
    public $email;
    public $password;
    public $type;
    public $phone;
    public $join_date;
    public $last_login;

    function create($name, $email, $password, $phone)
    {
        $user_id = 0;
        try
        {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("INSERT INTO user (name, email, password, phone) VALUES (:name, :email, :password, :phone)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':phone', $phone);
            $stmt->execute();

            $user_id = $this->conn->lastInsertId(); 

            $this->conn->commit();
        }
        catch (Exception $e)
        {
            $this->conn->rollBack();
            http_response_code(500);
            die('Error connecting to database');
        }

        $this->read($user_id);
    }

    function read($user_id)
    {
        $record = NULL;

        try
        {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("SELECT * FROM user WHERE user_id = :user_id");
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();
            $this->conn->commit();

            $record = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        catch (Exception $e)
        {
            $this->conn->rollBack();
            http_response_code(500);
            die('Error connecting to database');
        }

        $this->user_id = $record["user_id"];
        $this->name = $record["name"];
        $this->email = $record["email"];
        $this->password = $record["password"];
        $this->type = $record["type"];
        $this->phone = $record["phone"];
        $this->join_date = strtotime($record["join_date"]);
        $this->last_login = strtotime($record["last_login"]);
    }

    function update()
    {
        try
        {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("UPDATE user SET name = :name, email = :email, password = :password, type = :type, phone = :phone, last_login = FROM_UNIXTIME(:last_login) WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':type', $this->type);
            $stmt->bindParam(':phone', $this->phone);
            $stmt->bindParam(':last_login', $this->last_login);
            $stmt->bindParam(':password', $this->password);
            $stmt->execute();

            $this->conn->commit();
        }
        catch (Exception $e)
        {
            $this->conn->rollBack();
            http_response_code(500);
            die('Error connecting to database');
        }
    }

    function delete()
    {
        try
        {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("DELETE FROM user WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->execute();

            $this->conn->commit();
        }
        catch (Exception $e)
        {
            $this->conn->rollBack();
            http_response_code(500);
            die('Error connecting to database');
        }
    }
}
?>