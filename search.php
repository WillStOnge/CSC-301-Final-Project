<!DOCTYPE html>
<html lang="en-us">

<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/halfmoon@1.1.0/css/halfmoon-variables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/halfmoon@1.1.0/js/halfmoon.min.js"></script>
    <title>Login</title>
</head>

<body class="dark-mode">
    <div class="container">
    <?php
        session_start();

        if (isset($_SESSION['user_id']))
            echo '<div class="text-right pt-5">
                    <small class="align-text-top">Welcome back, ' . str_replace("%\n%", '', $_SESSION['user_name']) . '</small>
                    <a href="logout.php"><button type="button" class="btn btn-sm">Logout</button></a>
                  </div>';
        else
        {
            echo '<div class="text-right pt-5">
                    <a href="login.php"><button type="button" class="btn btn-sm">Login</button></a>
                    <a href="register.php"><button type="button" class="btn btn-sm">Register</button></a>
                  </div>';
            die('Must be signed into perform this action');
        }
    ?>
    </div>
</body>

</html>