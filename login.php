<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Terrarien Überwachung</title>

    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php
include "menu/nav.html";
SESSION_START();
// error message
$error_mes = "";


//connect to Database
$terra = new PDO('sqlite:db/terra.sqlite');
$login = $terra->query('SELECT * FROM login');
foreach ($login as $row) {
    $algo = $row['salt'];
    $password = $row['password'];
}

if ($_POST['submit'] == 'true'){
    if ($password == hash($algo, $_POST['password'])) {
        $_SESSION['login'] = 'true';
        $_SESSION['logout'] = 'false';
        header('location: index.php');
    } else {
        $_SESSION['login'] = 'false';
        $error_mes = "Falsches Passwort!";
    }
}


//disconnect database
$terra = NULL;
?>
<div class="col-md-12">
    <h3>Login:</h3>
    <form class="form-horizontal" action="login.php" method="post">

        <div class="form-group optional">
            <label class="col-sm-1 control-label" for="password">Passwort:</label>
            <div class="col-sm-2">
                <input class="form-control" id="password" type="password" name="password" maxlength="40" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-1 control-label" for="submit"></label>
            <div class="col-sm-2">
                <button class="btn btn-default" type="submit" id="submit" name="submit" value="true">Login</button>
            </div>
        </div>

        <div class="form-group">
            <span style="color: red; "><?php echo $error_mes; ?></span>
        </div>
    </form>
</div>
</body>
</html>