<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Terrarien Überwachung</title>
    
    <link rel="stylesheet" href="css/style.css">

    <!--google charts -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="js/dayView.js"></script>


</head>
<body>


<?php
include "menu/nav.html";
SESSION_START();

/*
 * set counter to switch through max last 6 day
 * it is possible to switch forward, back and to the current date
 */
if ($_POST['today'] == 'true') {
    $_SESSION['counter'] = 0;
} elseif ($_POST['back'] == 'true') {
    if ($_SESSION['counter'] > -6) {
        $_SESSION['counter'] -= 1;
    }
} elseif ($_POST['forward'] == 'true') {
    if ($_SESSION['counter'] < 0) {
        $_SESSION['counter'] += 1;
    }
} else {
    $_SESSION['counter'] = 0;
}
$counter = $_SESSION['counter'];
$_SESSION['print_day'] = date('Y-m-d', strtotime("$counter days"));


?>
<div class="col-md-12">
    <form class="form-inline" action="dayView.php" method="post" style="margin: 1em">
        <button class="btn btn-default" type="submit" name="back" value="true">zurück</button>
        <button class="btn btn-default" type="submit" name="forward" value="true">vor</button>
        <button class="btn btn-default" type="submit" name="today" value="true">heute</button>
    </form>


    <h3><?php echo $_SESSION['print_day']; ?></h3>
    <h3>Temperatur:</h3>
    <div id="linechart_material_temp"></div>
    <h3>Luftfeuchtigkeit:</h3>
    <div id="linechart_material_hum"></div>
</div>
</body>
</html>
