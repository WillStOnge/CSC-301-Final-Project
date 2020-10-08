<?php
include_once "utils/iobject.php";

class Review extends IObject
{
    public $review_id;
    public $user_id;
    public $worker_id;
    public $star_rating;
    public $description;
    public $create_date;

    static function create($user_id, $worker_id, $star_rating, $description)
    {
        $review = new Review();

        $review_id = 0;
        try
        {
            $review->conn->beginTransaction();
            $stmt = $review->conn->prepare("INSERT INTO review (user_id, worker_id, star_rating, description) VALUES (:user_id, :worker_id, :star_rating, :description)");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':worker_id', $worker_id, PDO::PARAM_INT);
            $stmt->bindParam(':star_rating', $star_rating);
            $stmt->bindParam(':description', $description);
            $stmt->execute();

            $review_id = $review->conn->lastInsertId(); 

            $review->conn->commit();
        }
        catch (Exception $e)
        {
            $review->conn->rollBack();
            http_response_code(500);
            die('Error 500' . $e->getMessage());
        }

        return $review->read($review_id);
    }

    static function read($review_id)
    {
        $record = NULL;
        $review = new Review();

        try
        {
            $review->conn->beginTransaction();
            $stmt = $review->conn->prepare("SELECT * FROM review WHERE review_id = :review_id");
            $stmt->bindParam(":review_id", $review_id, PDO::PARAM_INT);
            $stmt->execute();
            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($record === false)
                throw new Exception("Record not found.");

            $review->conn->commit();
        }
        catch (Exception $e)
        {
            $review->conn->rollBack();
            http_response_code(500);
            die('Error 500');
        }

        $review->review_id = $record['review_id'];
        $review->user_id = $record['user_id'];
        $review->worker_id = $record['worker_id'];
        $review->star_rating = $record['star_rating'];
        $review->description = $record['description'];
        $review->create_date = $record['create_date'];

        return $review;
    }

    function update()
    {
        try
        {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("UPDATE review SET user_id = :user_id, worker_id = :worker_id, star_rating = :star_rating, description = :description WHERE review_id = :review_id");
            $stmt->bindParam(':review_id', $this->review_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindParam(':worker_id', $this->worker_id, PDO::PARAM_INT);
            $stmt->bindParam(':star_rating', $this->star_rating);
            $stmt->bindParam(':description', $this->description);
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
            $stmt = $this->conn->prepare("DELETE FROM review WHERE review_id = :review_id");
            $stmt->bindParam(':review_id', $this->review_id, PDO::PARAM_INT);
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