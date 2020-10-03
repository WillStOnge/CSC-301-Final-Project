<?php
include_once "utils/iobject.php";

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
    public $banned;

    static function create($name, $email, $password, $phone)
    {
        $user = new User();

        $user_id = 0;
        try
        {
            $user->conn->beginTransaction();
            $stmt = $user->conn->prepare("INSERT INTO user (name, email, password, phone) VALUES (:name, :email, :password, :phone)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
            $stmt->execute();

            $user_id = $user->conn->lastInsertId(); 

            $user->conn->commit();
        }
        catch (Exception $e)
        {
            $user->conn->rollBack();
            http_response_code(500);
            die('Error 500');
        }

        return $user->read($user_id);
    }

    static function read($user_id)
    {
        $record = NULL;
        $user = new User();

        try
        {
            $user->conn->beginTransaction();
            $stmt = $user->conn->prepare("SELECT * FROM user WHERE user_id = :user_id");
            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($record === false)
                throw new Exception("Record not found.");

            $user->conn->commit();
        }
        catch (Exception $e)
        {
            $user->conn->rollBack();
            http_response_code(500);
            die('Error 500');
        }

        $user->user_id = $record["user_id"];
        $user->name = $record["name"];
        $user->email = $record["email"];
        $user->password = $record["password"];
        $user->type = $record["type"];
        $user->phone = $record["phone"];
        $user->banned = $record["banned"];
        $user->join_date = strtotime($record["join_date"]);
        $user->last_login = strtotime($record["last_login"]);

        return $user;
    }

    static function find($email)
    {
        $record = NULL;
        $user = new User();

        try
        {
            $user->conn->beginTransaction();
            $stmt = $user->conn->prepare("SELECT * FROM user WHERE email = :email");
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($record === false)
                throw new Exception("Record not found.");

            $user->conn->commit();
        }
        catch (Exception $e)
        {
            $user->conn->rollBack();
            http_response_code(500);
            die('Error 500' . $e->getMessage());
        }

        $user->user_id = $record["user_id"];
        $user->name = $record["name"];
        $user->email = $record["email"];
        $user->password = $record["password"];
        $user->type = $record["type"];
        $user->phone = $record["phone"];
        $user->banned = $record["banned"];
        $user->join_date = strtotime($record["join_date"]);
        $user->last_login = strtotime($record["last_login"]);

        return $user;
    }

    function update()
    {
        try
        {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("UPDATE user SET name = :name, email = :email, password = :password, type = :type, phone = :phone, banned = :banned, last_login = FROM_UNIXTIME(:last_login) WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':type', $this->type);
            $stmt->bindParam(':phone', $this->phone, PDO::PARAM_INT);
            $stmt->bindParam(':last_login', $this->last_login, PDO::PARAM_INT);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':banned', $this->banned, PDO::PARAM_BOOL);
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
            $stmt = $this->conn->prepare("DELETE FROM user WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
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