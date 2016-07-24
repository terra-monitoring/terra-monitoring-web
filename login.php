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
<h3>Login:</h3>
<form action="login.php" method="post">
    <input type="password" name="password" size="15" maxlength="40" required>
    <p></p>
    <button type="submit" name="submit" value="true">Login</button>
    <p></p>
    <span style="color: red; "><?php echo $error_mes; ?></span>
</form>
</body>
</html>