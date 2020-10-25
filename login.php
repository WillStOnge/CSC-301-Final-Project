<?php
session_start();
if (isset($_SESSION['user_id']))
    header('location: index.php');

if (isset($_POST['email']) && isset($_POST['password']))
{
    include_once "objects/user.php";

    if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))
    {
        $user = User::find($_POST['email']);

        if (password_verify($_POST['password'], $user->password))
        {
            if (!$user->banned)
            {
                $db = new Database();
                $worker;

                try
                {
                    $db->conn->beginTransaction();
                    $stmt = $db->conn->prepare("SELECT worker_id FROM worker WHERE user_id = :user_id");
                    $stmt->bindParam(':user_id', $user->user_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $db->conn->commit();
                    $worker = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                catch (Exception $e)
                {
                    $db->conn->rollBack();
                    http_response_code(500);
                    die('Error 500');
                }

                $_SESSION["user_id"] = $user->user_id;
                $_SESSION["user_name"] = $user->name;
                if ($worker !== false)
                    $_SESSION["worker_id"] = $worker["worker_id"];

                header('location: index.php');
            }
            else 
                echo "<p>User is banned</p>";
        }
        else
            echo "<p>Incorrect email or password</p>";
    }
    else
        echo "<p>Invalid email address</p>";
}
?>
<!DOCTYPE html>
<html lang="en-us">

<head>
    <meta charset="utf-8">
    <meta name="" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/halfmoon@1.1.0/css/halfmoon-variables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/halfmoon@1.1.0/js/halfmoon.min.js"></script>
    <title>Login</title>
</head>

<body class="dark-mode">
    <div class="container">
        <h1>Login</h1>
        <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post">
            <div class="form-group">
                <label>Email address</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn">Submit</button>
        </form>
    </div>
</body>

</html>