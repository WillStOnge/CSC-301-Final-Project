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
    function updateSkills(worker_id) {
        var request = new XMLHttpRequest();

        request.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.response)
                var div = document.getElementById('skills')
                div.innerHTML = ''

                title = document.createElement('h3')
                title.setAttribute('class', 'card-title text-center')
                title.innerText = 'Current Skills'

                div.appendChild(title)

                response.forEach((skill) => {
                    s = document.createElement('p')
                    s.innerText = skill.skill_name

                    div.appendChild(s)
                })
            }
        };
        request.open("GET", "api/getSkills.php?worker_id=" + worker_id, true);
        request.send();
    }

    function addSkill(worker_id) {
        if (worker_id.length == 0)
            return;
        else {
            var skill_name = document.getElementById('skill_name').value;
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    updateSkills(worker_id)
                }
            };
            request.open("POST", "api/addSkill.php?worker_id=" + worker_id + "&skill=" + skill_name, true);
            request.send();
        }
    }

    function removeSkill(worker_id) {
        if (worker_id.length == 0)
            return;
        else {
            var skill_name = document.getElementById('skill_name').value;
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    updateSkills(worker_id)
                }
            };
            request.open("POST", "api/removeSkill.php?worker_id=" + worker_id + "&skill=" + skill_name, true);
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
            die('<p class="align-text-top">You must be logged in as a worker to use this page.</p>');
        else
        {
            $user = User::read($_SESSION['user_id']);
            if (!isset($_SESSION['worker_id']))
                die('<p>You must be logged in as a worker to use this page.</p>');
        }
        ?>
        <div class="text-right pt-5">
            <small class="align-text-top">Welcome back,
                <?php echo str_replace("%\n%", '', $_SESSION['user_name']) ?></small>
            <a href="logout.php"><button type="button" class="btn btn-sm">Logout</button></a>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="card" id="skills">
                    <script>
                    updateSkills(<?php echo $_SESSION['worker_id']; ?>)
                    </script>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card">
                    <h3 class="card-title text-center">Add/Remove Skill</h3>
                    <div class="form-group" action="">
                        <label>Skill Name</label>
                        <input type="text" class="form-control" id="skill_name">
                    </div>
                    <a onclick="addSkill(<?php echo $_SESSION['worker_id']; ?>)"><button type="button" class="btn btn-sm">Add Skill</button></a>
                    <a onclick="removeSkill(<?php echo $_SESSION['worker_id']; ?>)"><button type="button" class="btn btn-sm">Remove Skill</button></a>
                </div>
            </div>
        </div>
    </div>
</body>