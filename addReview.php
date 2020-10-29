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

            echo '<div class="row">';

            echo '<div class="col-sm-6 align-text-top pt-5"><a href="index.php"><button type="button" class="btn btn-sm">Home</button></a></div>';

            if (isset($_SESSION['user_id']))
                echo '<div class="col-sm-6 text-right pt-5">
                        <small class="align-text-top">Welcome back, ' . str_replace("%\n%", '', $_SESSION['user_name']) . '</small>
                        <a href="logout.php"><button type="button" class="btn btn-sm">Logout</button></a>
                    </div>';
            else
            {
                echo '<div class="col-sm-6 text-right pt-5">
                        <a href="login.php"><button type="button" class="btn btn-sm">Login</button></a>
                        <a href="register.php"><button type="button" class="btn btn-sm">Register</button></a>
                    </div>';
                die('<p>Must be signed in to perform this action</p>');
            }

            echo '</div>';

            if (!isset($_GET['id']))
                die("<p class='text-center'>Invalid worker id</p>");

            include_once "objects/worker.php";
            include_once "objects/user.php";
            
            $worker = Worker::read($_GET['id']);
            $user = User::read($worker->user_id);

            if ($worker->status == "NOT APPROVED")
                die("<p class='text-center'>Invalid worker id</p>");
        
            echo '<title>Review ' . $user->name . '</title>';

            if (isset($_POST['star_rating']) && isset($_POST['description']))
            {
                if ($_POST['star_rating'] >= 1 && $_POST['star_rating'] <= 5)
                {
                    if (strlen($_POST['description']) >= 100)
                    {
                        include_once "objects/review.php";

                        Review::create($user->user_id, $worker->worker_id, $_POST['star_rating'], $_POST['description']);

                        header("location: worker.php?id=" . $worker->worker_id);
                    }
                    else 
                        echo "<p>Description must contain 100 characters</p>";
                }
                else 
                    echo "<p>Invalid star rating</p>";
            }
        ?>
        <h1>Add Review</h1>
        <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post">
            <div class="form-group">
                <label>Star Rating</label>
                <input type="number" class="form-control" name="star_rating" min="1" max="5" value="<?php echo isset($_POST['star_rating']) ? $_POST['star_rating'] : 3 ?>" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea class="form-control" placeholder="Enter your review here (minimum of 100 words)." name="description"><?php echo isset($_POST['description']) ? $_POST['description'] : ''; ?></textarea>
            </div>
            <button type="submit" class="btn">Submit</button>
        </form>
    </div>
</body>

</html>