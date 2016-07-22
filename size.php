<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Terrarien Überwachung</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/toggleButton.css">

    <!--google charts -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="js/size.js"></script>
</head>
<body>

<?php
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
if ($_POST['password'] == 'terra5#') {
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

} elseif (!empty($_POST['password'])) {
    $error_mes = "falsches Passwort!";
}

//disconnect database
$terra = NULL;
?>
<h3>Neue Messwerte Anlegen:</h3>
<h4>(Letzter Eintrag am <?php echo $last_date;?>):</h4>
<form action="size.php" method="post">
    <table>
        <tbody>
        <tr>
            <td style="width: 50px">Datum:</td>
            <td>
                <input type="date" name="date" style="width: 130px">
            </td>
        </tr>
        <tr>
            <td>Gewicht:</td>
            <td>
                <input type="number" min="1" max="100" step="0.1" name="weight" style="width: 130px">
            </td>
            <td>g</td>
        </tr>
        <tr>
            <td>Länge:</td>
            <td>
                <input type="number" min="1" max="100" step="0.1" name="length" style="width: 130px">
            </td>
            <td>cm</td>
        </tr>
        </tbody>
    </table>
    <p>
        <label for="passwd">Passwort:</label>
        <input type="password" id="passwd" name="password" size="14" maxlength="40" required>
    <p>
        <span style="color: red; "><?php echo $error_mes; ?></span>
        <span style="color: green; "><?php echo $success_mes; ?></span>
    <p>
        <button type="submit">Hinzufügen</button>
</form>
<h3>Gewicht und Länge:</h3>
<div id="linechart_material_size"></div>
</body>
</html>