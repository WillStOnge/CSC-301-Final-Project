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
                        var outerdiv = document.createElement('div')
                        var div = document.createElement('div')
                        var header = document.createElement('h5')
                        var created_on = document.createElement('p')
                        var body = document.createElement('p')

                        outerdiv.setAttribute('class', 'col-sm-6')
                        div.setAttribute('class', 'card')
                        header.setAttribute('class', 'card-title')

                        header.textContent = review.name + " - "
                        created_on.textContent = review.create_date
                        body.textContent = review.description

                        for (var i = 0; i < review.star_rating; i++)
                            header.innerHTML += "&#11088;"

                        reviews.appendChild(outerdiv)
                        outerdiv.appendChild(div)
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
            include_once "objects/utils/database.php";
            
            $worker = Worker::read($_GET['id']);
            $user = User::read($worker->user_id);

            if ($worker->status == "NOT APPROVED")
                die("<p class='text-center'>Invalid worker id</p>");
        
            echo '<title>' . $user->name . '</title>';
        ?>
        <div class="card">
            <div class="row">
                <div class="col-sm-8">
                    <?php
                    $db = new Database();

                    try
                    {
                        $db->conn->beginTransaction();
                        $stmt = $db->conn->prepare("SELECT AVG(star_rating) FROM review WHERE worker_id = :worker_id");
                        $stmt->bindParam(":worker_id", $worker->worker_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $rating = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        $db->conn->commit();
                    }
                    catch (Exception $e)
                    {
                        $db->conn->rollBack();
                        http_response_code(500);
                        die('Error 500');
                    }

                    $str = "";

                    for ($i = 0; $i < floor($rating[0]["AVG(star_rating)"]); $i++)
                        $str .= "&#11088;";

                    echo '<u><h3 class="card-title">' . ucwords($user->name) . ' - ' . $worker->location . '</u> - ' . $str . '</h3>';

                    echo '<p class="text-justify">' . $worker->description . '</p>';
                    ?>
                </div>
                <div class="col-sm-4">
                    <div class="text-right pt-5">
                        <a href="mailto:<?php echo $user->email; ?>"><button type="button"
                                class="btn btn-sm">Email</button></a>
                        <a href="tel:<?php echo $user->phone; ?>"><button type="button"
                                class="btn btn-sm">Call</button></a>
                        <?php
                        if ($_SESSION['worker_id'] == $worker->worker_id)
                            echo '<a href="workerEdit.php"><button type="button" class="btn btn-sm">Edit Profile</button></a>';
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <h3 class="card-title text-center">Reviews</h3>
        <a href="addReview.php?id=<?php echo $worker->worker_id; ?>"><button type="button"class="btn btn-sm">New Review</button></a>
        <div id="reviews" class="row">
            <script>
            getReviews(<?php echo $worker->worker_id; ?>)
            </script>
        </div>
    </div>
</body>

</html>