<?php
session_start();
if (isset($_SESSION['user_id']))
    header('location: index.php');

if (isset($_POST['email']) && isset($_POST['password']))
{
    include_once "objects/user.php";

    if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))
    {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $user = User::find($_POST['email']);

        if ($password != $user->password)
        {
            $_SESSION["user_id"] = $user->user_id;
            echo $user->banned;

            header('location: index.php');
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
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>

</html>