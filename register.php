<?php
session_start();
if (isset($_SESSION['user_id']))
    header('location: index.php');

if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['pnum']))
{
    include_once "objects/user.php";

    if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))
    {
        if (preg_match('/^[0-9]{10}+$/', $_POST['pnum']))
        {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $name = htmlspecialchars($_POST['name']);

            $user = User::create($name, $_POST['email'], $password, $_POST['pnum']);

            session_start();
            $_SESSION["user_id"] = $user->user_id;

            header('location: index.php');
        }
        else
            echo "<p>Invalid phone number (Example: 1234567890)</p>";
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
    <title>Register</title>
</head>

<body class="dark-mode">
    <div class="container">
        <h1>Register account</h1>
        <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post">
            <div class="form-group">
                <label>Full name</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="form-group">
                <label>Email address</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="form-group">
                <label>Phone number</label>
                <input type="tel" class="form-control" name="pnum">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn">Register</button>
        </form>
    </div>
</body>

</html>