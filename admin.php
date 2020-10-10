<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en-us">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Home page</title>
    <link href="https://cdn.jsdelivr.net/npm/halfmoon@1.1.0/css/halfmoon-variables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/halfmoon@1.1.0/js/halfmoon.min.js"></script>
    <script>
    function updateUser(email) 
    {
        if (email.length == 0) 
        {
            document.getElementById("userData").innerHTML = "";
            return;
        } 
        else 
        {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) 
                {
                    var response = JSON.parse(this.response)

                    var join_date = new Date(response.join_date * 1000)

                    document.getElementById("user_id").innerHTML = response.user_id;
                    document.getElementById("name").innerHTML = response.name;
                    document.getElementById("email").innerHTML = response.email;
                    document.getElementById("type").innerHTML = response.type;
                    document.getElementById("phone").innerHTML = response.phone;
                    document.getElementById("join_date").innerHTML = join_date.toString();
                    document.getElementById("banned").innerHTML = response.banned == 0 ? 'false' : 'true';
                    document.getElementById("is_admin").innerHTML = response.is_admin == 0 ? 'false' : 'true';
                }
            };
            request.open("GET", "api/user.php?email=" + email, true);
            request.send();
        }
    }

    function admin()
    {
        var user_id = document.getElementById('user_id').innerHTML;

        if (user_id.length == 0) 
            return;
        else 
        {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) 
                {
                    var response = JSON.parse(this.response)
                    
                    document.getElementById("is_admin").innerHTML = response.newValue;
                }
            };
            request.open("POST", "api/admin.php?user_id=" + user_id, true);
            request.send();
        }
    }

    function ban()
    {
        var user_id = document.getElementById('user_id').innerHTML;

        if (user_id.length == 0) 
            return;
        else 
        {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) 
                {
                    var response = JSON.parse(this.response)
                    
                    document.getElementById("banned").innerHTML = response.newValue;
                }
            };
            request.open("POST", "api/ban.php?user_id=" + user_id, true);
            request.send();
        }
    }
    </script>
</head>

<body class="dark-mode">
    <div class="container">
        <?php
        include_once "objects/user.php";

        if (!isset($_SESSION['user_id']))
            die('<p class="align-text-top">You must be logged in as an admin to use this page.</p>');
        else
        {
            $user = User::read($_SESSION['user_id']);
            if (!$user->is_admin)
                die('<p>You must be logged in as an admin to use this page.</p>');
        }
        ?>
        <div class="text-right pt-5">
            <?php
            echo '<small class="align-text-top">Welcome back, ' . str_replace("%\n%", '', $_SESSION['user_name']) . '</small>
            <a href="logout.php"><button type="button" class="btn btn-sm">Logout</button></a>';
            ?>
        </div>
        <div style="width:30%">
            <div class="form-group" action="">
                <label>User Email</label>
                <input type="email" class="form-control" name="name" onkeyup="updateUser(this.value)">
            </div>
            <div id="userData">
            <table class="table table-hover table-responsive table-inner-bordered">
                <tbody>
                    <tr>
                        <th>User Id</th>
                        <td id="user_id"></td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td id="name"></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td id="email"></td>
                    </tr>
                    <tr>
                        <th>Type</th>
                        <td id="type"></td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td id="phone"></td>
                    </tr>
                    <tr>
                        <th>Join Date</th>
                        <td id="join_date"></td>
                    </tr>
                    <tr>
                        <th>Banned</th>
                        <td id="banned"></td>
                    </tr>
                    <tr>
                        <th>Admin</th>
                        <td id="is_admin"></td>
                    </tr>
                </tbody>
            </table>
            <br/>
            <div class="text-center">
                <button type="button" class="btn btn-sm" onclick="admin()">Toggle Admin</button>
                <button type="button" class="btn btn-sm btn-red" onclick="ban()">Toggle Ban</button>
            </div>
        </div>
    </div>
</body>