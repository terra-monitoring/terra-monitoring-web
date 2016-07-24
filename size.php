<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Terrarien Überwachung</title>

    <link rel="stylesheet" href="css/style.css">

    <!--google charts -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="js/size.js"></script>
</head>
<body>

<?php
SESSION_START();
include "menu/nav.html";
// error message
$error_mes = "";
// success message
$success_mes = "";

//connection to database
$einstein = new PDO('sqlite:db/einstein.sqlite');

// get Last date
$last_insert = $einstein->query('SELECT date FROM size');
foreach ($last_insert as $row) {
    $last_date = date("d.m.Y", strtotime($row['date']));
}

// after push "Hinzufügen" this if-query put the new data in database
if ($_SESSION['login'] == 'true' && $_POST['submit'] == 'true') {
    $date = $_POST['date'];
    $length = $_POST['length'];
    $weight = $_POST['weight'];

    $einstein->exec("INSERT INTO size VALUES('$date', $length, $weight)");

    //data check
    $last_insert = $einstein->query('SELECT date FROM size');
    foreach ($last_insert as $row) {
        $last_date = $row['date'];
    }
    if($date = $last_date){
        $success_mes = "Die Messwerte wurden erfolgreich hinzugefügt.";
    } else {
        $error_mes = "Das Hinzufügen hat nicht funktioniert!";
    }
    header('location: size.php');
} elseif ($_POST['submit'] == 'true') {
    $error_mes = "Bitte erst einloggen!";
}

//disconnect database
$terra = NULL;
?>
<div class="col-md-12">
    <h3>Neue Messwerte Anlegen:</h3>
    <h4>(Letzter Eintrag am <?php echo $last_date;?>):</h4>
        <form class="form-horizontal" action="size.php" method="post">

            <div class="form-group optional">
                <label class="col-sm-1 control-label" for="date">Datum:</label>
                <div class="col-sm-2">
                    <input class="form-control" type="date" id="date" name="date" required>
                </div>
            </div>

            <div class="form-group optional">
                <label class="col-sm-1 control-label" for="weight">Gewicht:</label>
                <div class="col-sm-2">
                    <input class="form-control" type="number" id="weight" min="1" max="100" step="0.1" name="weight" placeholder="in g" required>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-1 control-label" for="lenght">Länge:</label>
                <div class="col-sm-2">
                    <input class="form-control" type="number" id="lenght" min="1" max="100" step="0.1" name="length" placeholder="in cm" required>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-1 control-label" for="submit"></label>
                <div class="col-sm-2">
                    <button class="btn btn-default" type="submit" id="submit" name="submit" value="true">Hinzufügen</button>
                </div>
            </div>

            <div class="form-group">
                <span style="color: red; "><?php echo $error_mes; ?></span>
                <span style="color: green; "><?php echo $success_mes; ?></span>
            </div>

        </form>
</div>
<div class="col-md-12">
    <h3>Gewicht und Länge:</h3>
    <div id="linechart_material_size"></div>
</div>
</body>
</html>