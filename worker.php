<!DOCTYPE html>
<html lang="en-us">

<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/halfmoon@1.1.0/css/halfmoon-variables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/halfmoon@1.1.0/js/halfmoon.min.js"></script>
    <script>
    function getReviews(worker_id) {
        if (worker_id.length == 0)
            return;
        else {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.response)
                    var reviews = document.getElementById('reviews')

                    response.forEach((review) => {
                        var div = document.createElement('div')
                        var header = document.createElement('h5')
                        var created_on = document.createElement('p')
                        var body = document.createElement('p')

                        div.setAttribute('class', 'card')
                        header.setAttribute('class', 'card-title')

                        header.textContent = review.name + " - "
                        created_on.textContent = review.create_date
                        body.textContent = review.description

                        for (var i = 0; i < review.star_rating; i++)
                            header.innerHTML += "&#11088;"

                        reviews.appendChild(div)
                        div.appendChild(header)
                        div.appendChild(created_on)
                        div.appendChild(body)
                    })
                }
            };
            request.open("GET", "api/reviews.php?worker_id=" + worker_id, true);
            request.send();
        }
    }
    </script>
</head>

<body class="dark-mode">
    <div class="container-fluid">
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

            if (!isset($_GET['id']))
                die("<p class='text-center'>Invalid worker id</p>");

            include_once "objects/worker.php";
            include_once "objects/user.php";
            
            $worker = Worker::read($_GET['id']);
            $user = User::read($worker->user_id);

            if ($worker->status == "NOT APPROVED")
                die("<p class='text-center'>Invalid worker id</p>");
        
            echo '<title>' . $user->name . '</title>';
        ?>
        <div class="card">
            <h3 class="card-title"><?php echo ucwords($user->name) . '\'s Profile' ?></h3>
            <img src="<?php echo 'assets/avatars/' . $worker->avatar_name ?>" class="img-fluid rounded-circle"
                style="object-fit:cover; width:230px; height:230px;" />
        </div>
        <div class="card">
            <h3 class="card-title">Description</h3>
            <?php
            echo '<p>' . $worker->description . '</p>';
            ?>
        </div>
        <div id="reviews">
            <h3 class="card-title text-center">Worker Reviews</h3>
            <script>
            getReviews(<?php echo $worker->worker_id; ?>)
            </script>
        </div>
    </div>
</body>

</html>