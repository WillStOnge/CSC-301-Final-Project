<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en-us">

<head>
    <meta charset="utf-8">
    <title>Home page</title>
    <link href="https://cdn.jsdelivr.net/npm/halfmoon@1.1.0/css/halfmoon-variables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/halfmoon@1.1.0/js/halfmoon.min.js"></script>
    <script>
    function updateUser(email) {
        if (email.length == 0)
            return;
        else {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.response)

                    var join_date = new Date(response.join_date * 1000)

                    console.log(response);

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

    function admin() {
        var user_id = document.getElementById('user_id').innerHTML;

        if (user_id.length == 0)
            return;
        else {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.response)

                    document.getElementById("is_admin").innerHTML = response.newValue;
                }
            };
            request.open("POST", "api/admin.php?user_id=" + user_id, true);
            request.send();
        }
    }

    function ban() {
        var user_id = document.getElementById('user_id').innerHTML;

        if (user_id.length == 0)
            return;
        else {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.response)

                    document.getElementById("banned").innerHTML = response.newValue;
                }
            };
            request.open("POST", "api/ban.php?user_id=" + user_id, true);
            request.send();
        }
    }

    function deleteUser() {
        var user_id = document.getElementById('user_id').innerHTML;

        if (user_id.length == 0)
            return;
        else {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var email = document.getElementById('email').value
                    updateUser(email)
                }
            };
            request.open("POST", "api/deleteUser.php?user_id=" + user_id, true);
            request.send();
        }
    }

    function updateWorkers() {
        var request = new XMLHttpRequest();

        request.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.response)
                var queue = document.getElementById('queue')
                queue.innerHTML = ''

                var row = document.createElement('tr')
                var header1 = document.createElement('th')
                var header2 = document.createElement('th')
                var header3 = document.createElement('th')
                var header4 = document.createElement('th')

                header1.innerText = 'Worker Id'
                header2.innerText = 'Name'
                header3.innerText = 'Document'
                header4.innerText = 'Buttons'

                queue.appendChild(row)
                row.appendChild(header1)
                row.appendChild(header2)
                row.appendChild(header3)
                row.appendChild(header4)

                response.forEach((worker) => {
                    var row = document.createElement('tr')

                    var dataId = document.createElement('td')
                    dataId.innerText = worker.worker_id

                    var dataName = document.createElement('td')
                    dataName.innerText = worker.name

                    var dataDoc = document.createElement('td')
                    var anchor = document.createElement('a')
                    anchor.setAttribute('href', 'assets/certifications/' + worker.worker_id + '/' + worker.file_name)
                    anchor.setAttribute('target', 'about:blank')
                    anchor.innerText = worker.file_name

                    var buttons = document.createElement('td')
                    var a1 = document.createElement('a')
                    var a2 = document.createElement('a')
                    var button1 = document.createElement('button')
                    var button2 = document.createElement('button')

                    a1.setAttribute('onclick', 'approve(' + worker.worker_id + ')')
                    a2.setAttribute('onclick', 'deny(' + worker.worker_id + ')')
                    button1.setAttribute('type', 'button')
                    button1.setAttribute('class', 'btn btn-sm btn-rounded')
                    button1.innerText = 'Approve'
                    button2.setAttribute('type', 'button')
                    button2.setAttribute('class', 'btn btn-sm btn-danger btn-rounded')
                    button2.innerText = 'Deny'

                    queue.appendChild(row)
                    row.appendChild(dataId)
                    row.appendChild(dataName)
                    row.appendChild(dataDoc)
                    row.appendChild(buttons)
                    dataDoc.appendChild(anchor);
                    buttons.appendChild(a1)
                    buttons.appendChild(a2)
                    a1.appendChild(button1)
                    a2.appendChild(button2)
                })
            }
        };
        request.open("GET", "api/approval.php", true);
        request.send();
    }

    function deny(worker_id) {
        if (worker_id.length == 0)
            return;
        else {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    updateWorkers()
                }
            };
            request.open("POST", "api/denyWorker.php?worker_id=" + worker_id, true);
            request.send();
        }
    }

    function approve(worker_id) {
        if (worker_id.length == 0)
            return;
        else {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    updateWorkers()
                }
            };
            request.open("POST", "api/approveWorker.php?worker_id=" + worker_id, true);
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
        ?>
        <div class="row">
            <div class="col-sm-7">
                <div class="card">
                    <h3 class="card-title text-center">Worker Approval Queue</h3>

                    <table class="table table-hover table-inner-bordered">
                        <tbody id="queue">
                            <script>
                            updateWorkers()
                            </script>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-sm-5">
                <div class="card">
                    <h3 class="card-title text-center">User Administration</h3>
                    <div class="form-group" action="">
                        <label>User Email</label>
                        <input type="email" class="form-control" name="name" onkeyup="updateUser(this.value)">
                    </div>
                    <div id="userData">
                        <table class="table table-hover table-inner-bordered">
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
                    </div>
                    <br />
                    <div class="text-center">
                        <button type="button" class="btn btn-sm" onclick="admin()">Toggle Admin</button>
                        <button type="button" class="btn btn-sm btn-red" onclick="ban()">Toggle Ban</button>
                        <button type="button" class="btn btn-sm" onclick="deleteUser()">Delete User</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>