<!DOCTYPE html>
<html lang="en-us">

<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/halfmoon@1.1.0/css/halfmoon-variables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/halfmoon@1.1.0/js/halfmoon.min.js"></script>
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
                die('<p>Must be signed into perform this action</p>');
            }

            include_once "objects/worker.php";
            include_once "objects/user.php";
            
            if (!isset($_SESSION['worker_id']))
            {
                die('Must be signed in as a worker to use this page.');
            }

            $worker = Worker::read($_SESSION['worker_id']);
            $user = User::read($worker->user_id);

            echo '<title>' . $user->name . '</title>';

            if (isset($_POST['name']) && isset($_POST['description']) && isset($_POST['phone']) && isset($_POST['location']))
            {
                if (preg_match('/^[0-9]{10}+$/', $_POST['phone']))
                {
                    $worker->description = $_POST['description'];
                    $worker->location = $_POST['location'];
                    $user->name = $_POST['name'];
                    $user->phone = $_POST['phone'];

                    $user->update();
                    $worker->update();

                    header("location: worker.php?id=" . $worker->worker_id);
                }
                else 
                    echo "<p>Phone number invalid</p>";
            }
        ?>
        <h1>Edit Profile</h1>
        <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo $user->name; ?>" required>
                <label>Phone Number</label>
                <input type="tel" class="form-control" name="phone" value="<?php echo $user->phone; ?>" required>
                <label>Location</label>
                <input type="text" class="form-control" name="location" value="<?php echo $worker->location; ?>" required>
                <label>Description</label>
                <textarea class="form-control" placeholder="Enter your profile description here." name="description"><?php echo $worker->description; ?></textarea>
            </div>
            <button type="submit" class="btn">Submit</button>
        </form>
    </div>
</body>

</html>