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
                    die('[]');
                }

                $_SESSION["user_id"] = $user->user_id;
                $_SESSION["user_name"] = $user->name;
                if ($worker !== false)
                    $_SESSION["worker_id"] = $worker[0]["worker_id"];
                $_SESSION["is_admin"] = $user->is_admin;

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
        <?php
        echo '<div class="row">';

        echo '<div class="col-sm-6 align-text-top pt-5"><a href="index.php"><button type="button" class="btn btn-sm">Home</button></a></div>';

        if (isset($_SESSION['user_id']))
            echo '<div class="col-sm-6 text-right pt-5">
                    <small class="align-text-top">Welcome back, ' . str_replace("%\n%", '', $_SESSION['user_name']) . '</small>
                    <a href="logout.php"><button type="button" class="btn btn-sm">Logout</button></a>
                </div>';
        else
            echo '<div class="col-sm-6 text-right pt-5">
                    <a href="login.php"><button type="button" class="btn btn-sm">Login</button></a>
                    <a href="register.php"><button type="button" class="btn btn-sm">Register</button></a>
                </div>';

        echo '</div>';
        ?>
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