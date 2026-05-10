<?php
    session_start();
    require_once "../bl/user_manager.php";

    $usermanager = new UserManager();
    $users = $usermanager->getUser();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Document</title>
</head>
<body>
    <div class="col s4 m4 l4">
        <div class="row">
            <?php if(!empty($users)) :?>
                <a class="waves-effect waves-light btn-large" onclick = "redirectFunc(1)"><i class="material-icons right">add_circle</i>LOGIN</a>
                <?php endif ?>
        </div>
    </div>
<div class="col s6 m6 l6">

<div class="row">
    <div class="col s4 m4 l4"></div>
            
    <div class="col s4 m4 l4">
        <div class="row">

            <div class="input-field col s6 m6 l6">
                <i class="material-icons prefix">account_circle</i>
                <input id="FName" type="text" class="validate">
                <label for="FName">First Name</label>
            </div>

            <div class="input-field col s6 m6 l6">
                <i class="material-icons prefix">account_circle</i>
                <input id="LName" type="tel" class="validate">
                <label for="LName">Last Name</label>
            </div>

            <div class="col s12 m12 l12">
                <a class="waves-effect waves-light btn-large #1e88e5 blue darken-1" style="width: 100%;" onclick="addFunc()">
                    <i class="material-icons right">add_circle</i>
                    Add User
                </a>
            </div>

            <br>

            <div class="col s12 m12 l12">
                <table class="highlight centered" id="marivelesTable">

<thead> 

                    <tr>
                        <th>User ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Action</th>
                    </tr>

</thead>

                    <?php if (!empty($users)) : ?>
                        <?php foreach ($users as $index => $user) : ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $user['firstName'] ?></td>
                                <td><?= $user['lastName'] ?></td>
                                <td>
                                    <a class="waves-effect waves-light btn #fdd835 yellow darken-1" onclick="updateFunc(<?= $user['user_id'] ?>)" style="width: 100%; margin: 5px"><i class="material-icons right">refresh</i>Update</a>
                                    <a class="waves-effect waves-light btn #e53935 red darken-1" onclick="deleteFunc(<?= $user['user_id'] ?>)" style="width: 100%; margin: 5px"><i class="material-icons right">remove_circle_outline</i>Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td>No data found</td>
                        </tr>
                    <?php endif ?>
                </table>
            </div>

        </div>
    </div>

    <div class="col s4 m4 l4"></div>
</div>

<br>
    <script src = "../scripts/service.js"></script>
</body>
</html>
