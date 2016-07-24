<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Terrarien Überwachung</title>

    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php
SESSION_START();

$_SESSION['login'] = "false";
$_SESSION['logout'] = "true";

include "menu/nav.html";
?>
<div class="col-md-12">
    <h3>Erfolgreich Ausgeloggt!</h3>
</div>
</body>
</html>