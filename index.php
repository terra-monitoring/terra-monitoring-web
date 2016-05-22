<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Terrarien Überwachung</title>
    
    <link rel="stylesheet" href="css/style.css">

    <!--google charts -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="js/index.js"></script>

</head>
<body>

<?php
include "menu/nav.html";
SESSION_START();
//connect to Database
$terra = new PDO('sqlite:db/terra.sqlite');
// get last entry from table seven_day
$current = $terra->query('SELECT time, s1,s2,s3,s4 FROM seven_day ORDER BY time DESC LIMIT 1');
foreach ($current as $row) {
    $current_time = $row['time'];
    $current_temp1 = $row['s1'];
    $current_temp2 = $row['s2'];
    $current_temp3 = $row['s3'];
    $current_hum = $row['s4'];
}

// change format from current time
$formatted_time = date("d.m.Y H:i:s", strtotime($current_time));

//disconnect database
$terra = NULL;
?>
    <h3>Aktuell (<?php echo $formatted_time; ?>)</h3>
    <table>
        <tbody>
        <tr>
            <td>Temperatur oben:</td>
            <td><?php echo $current_temp1 ?> °C</td>
        </tr>
        <tr>
            <td>Temperatur mitte:</td>
            <td><?php echo $current_temp2 ?> °C</td>
        </tr>
        <tr>
            <td>Temperatur unten:</td>
            <td><?php echo $current_temp3 ?> °C</td>
        </tr>
        <tr>
            <td>Luftfeuchtigkeit:</td>
            <td><?php echo $current_hum ?> %</td>
        </tr>
        </tbody>
    </table>

    <h3>Temperatur Heute:</h3>
    <div id="linechart_material_temp"></div>
    <h3>Luftfeuchtigkeit Heute:</h3>
    <div id="linechart_material_hum"></div>
</body>
</html>