<!DOCTYPE html>
<html lang="en-us">

<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/halfmoon@1.1.0/css/halfmoon-variables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/halfmoon@1.1.0/js/halfmoon.min.js"></script>
    <title>Home page</title>
    <script>
    function search() {
        var request = new XMLHttpRequest();

        var query = document.getElementById('query').value
        var outer = document.getElementById('workers')

        outer.innerHTML = ''

        request.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.response)

                response.forEach((worker) => {
                    var card = document.createElement('div')
                    var title = document.createElement('h3')
                    var link = document.createElement('a')
                    var loc = document.createElement('h6')
                    var desc = document.createElement('p')

                    card.setAttribute('class', 'col-sm-5 card')
                    title.setAttribute('class', 'card-title')
                    link.setAttribute('class', 'hyperlink text-white')
                    link.setAttribute('href', 'worker.php?id=' + worker.worker_id)
                    desc.setAttribute('class', 'text-muted')

                    link.innerText = worker.name + ' - '
                    loc.innerText = worker.location
                    desc.innerText = worker.description

                    for (var i = 0; i < worker.rating; i++)
                        link.innerHTML += "&#11088;"

                    outer.appendChild(card)
                    card.appendChild(title)
                    title.appendChild(link)
                    card.appendChild(loc)
                    card.appendChild(desc)
                })
            }
        };
        request.open("GET", "api/search.php?query=" + query, true);
        request.send();
    }
    </script>
</head>

<body class="dark-mode">
    <div class="container">
        <div class="text-right pt-5">
            <?php
            session_start();

            echo '<div class="row"><div class="col-sm-6 align-text-top text-left pt-5">';

            if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'])
                echo '<a href="admin.php"><button type="button" class="btn btn-sm">Admin Page</button></a>';
            else if (!isset($_SESSION['worker_id']) && isset($_SESSION['user_id']))
                echo '<a href="application.php"><button type="button" class="btn btn-sm">Apply to be a Worker</button></a>';

            echo '</div>';

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
        </div>

        <div class="card">
            <h3 class="card-title text-center">Search for Workers</h3>
            <div class="row">
                <div class="col-sm">
                    <input type="text" class="form-control" placeholder="Enter skills here (space delimited)" id='query'>
                </div>
                <div class="col-sm pl-5">
                    <button onclick="search()" class="btn">Submit</button>
                </div>
            </div>
        </div>
        
        <div class="row" id="workers">
            
        </div>
    </div>
</body>

</html>