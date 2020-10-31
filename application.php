<?php
session_start();

if (isset($_SESSION['worker_id']))
    header('location: index.php');

if (isset($_POST['description']) && isset($_POST['location']) && isset($_POST['cert']))
{
    if (strlen($_POST['description']) <= 2048)
    {
        include_once "objects/worker.php";
        include_once "objects/certification.php";

        // TODO: Setup file handling for certification

        $worker = Worker::create($_SESSION['user_id'], $_POST['description'], $_POST['location']);
        Certification::create($worker->worker_id, $_POST['cert']);

        $_SESSION['worker_id'] = $worker->worker_id;

        header("location: index.php");
    }
    else
        echo "<p>Description is too long.</p>";
}
?>
<!DOCTYPE html>
<html lang="en-us">

<head>
    <meta charset="utf-8">
    <meta name="" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/halfmoon@1.1.0/css/halfmoon-variables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/halfmoon@1.1.0/js/halfmoon.min.js"></script>
    <title>Application</title>
</head>

<body class="dark-mode">
    <div class="container">
        <?php
        echo '<div class="row">';

        echo '<div class="col-sm-6 align-text-top pt-5"><a href="index.php"><button type="button" class="btn btn-sm">Home</button></a></div>';

        if (!isset($_SESSION['user_id']))
        {
            echo '<div class="col-sm-6 text-right pt-5">
                    <a href="login.php"><button type="button" class="btn btn-sm">Login</button></a>
                    <a href="register.php"><button type="button" class="btn btn-sm">Register</button></a>
                </div>';
            die('<p>Must be signed in to perform this action</p>');
        }

        echo '</div>';
        ?>
        <h1>Worker Application</h1>
        <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post">
            <div class="form-group">
                <label>Location</label>
                <input type="text" class="form-control" name="location" required>
                <label>Description</label>
                <textarea class="form-control" name="description" placeholder="Write your profile description here. This will be visible to everyone. (Max 2048 characters)" maxlength="2048" required></textarea>
                <label>Applicable Certification</label>
                <input type="file" class="form-control" name="cert" required>
            </div>
            <button type="submit" class="btn">Submit</button>
        </form>
    </div>
</body>

</html>