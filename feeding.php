
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Terrarien Überwachung</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/toggleButton.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
          integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>

    <script>
        //function to hide "manuel toggle button" if auto-mod is on-->
        $(document).ready(function () {
            if ($('#fastentag').is(':checked')) {
                $(".optional").hide();
            }
            $("#fastentag").click(function () {
                $(".optional").toggle();
            });
        });
    </script>

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
$last_insert = $einstein->query('SELECT date FROM feed');
foreach ($last_insert as $row) {
    $last_date = date("d.m.Y", strtotime($row['date']));
}


// get Futter for DropDown
$result = $einstein->query('SELECT name FROM food ORDER BY name COLLATE NOCASE');
foreach ($result as $row) {
    $FutterDropDown .= "<option>" . $row['name'] . "</option>";
}


// get all feeds
$resultFeed = $einstein->query('SELECT * FROM feed ORDER BY date DESC');
foreach ($resultFeed as $rowFeed) {

    $currentFoodID = $rowFeed['foodID'];
    if($currentFoodID != 0) {
        $resultFood = $einstein->query("SELECT name FROM food WHERE id = $currentFoodID");
        foreach ($resultFood as $rowFood) {
            $currentFood = $rowFood['name'];
        }
    } else {
        $currentFood = "";
    }

    if($rowFeed['Vitamine'] == 'true'){
        $currentVitamine = utf8_encode("&#10003");
    }else{
        $currentVitamine = "";
    }

    if($rowFeed['Calcium'] == 'true'){
        $currentCalcium = utf8_encode("&#10003");
    }else{
        $currentCalcium = "";
    }

    if($rowFeed['Fastentag'] == 'true'){
        $currentFastentag = utf8_encode("&#10003");
    }else{
        $currentFastentag = "";
    }

    $Fütterungen .= "<tr>
                        <td>" . date("d.m.Y", strtotime($rowFeed['date'])) . "</td>
                        <td>" . $currentFood . "</td>
                        <td>" . $rowFeed['menge'] . "</td>
                        <td>" . $currentVitamine . "</td>
                        <td>" . $currentCalcium . "</td>
                        <td>" . $currentFastentag . "</td>
                        <td>" . $rowFeed['Bemerkungen'] . "</td>
                    </tr>";
}


// after push "Hinzufügen" this if-query put the new data in database
if ($_SESSION['login'] == 'true' && $_POST['submit'] == 'true') {
    if ($_POST['fastentag'] == 'true'){
        $date = $_POST['date'];
        $bemerkung = $_POST['bemerkung'];

        $einstein->exec("INSERT INTO feed(date, Fastentag, Bemerkungen) VALUES('$date', 'true', '$bemerkung')");

        //data check
        $last_insert = $einstein->query('SELECT date FROM feed');
        foreach ($last_insert as $row) {
            $last_date = $row['date'];
        }
        if($date = $last_date){
            $success_mes = "Die Messwerte wurden erfolgreich hinzugefügt.";
        } else {
            $error_mes = "Das Hinzufügen hat nicht funktioniert!";
        }
    } else {
        $date = $_POST['date'];
        $food = $_POST['futter'];
        $menge = $_POST['menge'];

        if ($_POST['vitamine'] == 'true'){
            $vitamine = "true";
        } else {
            $vitamine = "false";
        }

        if ($_POST['calcium'] == 'true'){
            $calcium = "true";
        } else {
            $calcium = "false";
        }

        $bemerkung = $_POST['bemerkung'];

        $getFoodID = $einstein->query("SELECT * from food");
        foreach ($getFoodID as $row) {
            if ($food == $row['name'])
            $foodID = $row['id'];
        }

        $einstein->exec("INSERT INTO feed VALUES('$date', '$foodID', '$menge', '$vitamine', '$calcium', 'false', '$bemerkung')");

    }
    header('location: feeding.php');
} elseif ($_POST['submit'] == 'true') {
    $error_mes = "Bitte erst einloggen!";
}

//disconnect database
$terra = NULL;
?>
<div class="col-md-4" >
    <h3>Neue Fütterung:</h3>
    <h4>(Letzter Eintrag am <?php echo $last_date;?>):</h4>
        <form class="form-horizontal" action="feeding.php" method="post">

            <div class="form-group">
                <label class="col-sm-3 control-label" for="date" >Datum:</label>
                <div class="col-sm-6">
                    <input class="form-control" id="date" type="date" name="date" >
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="fastentag">Fastentag?</label>
                <div class="col-sm-6">
                    <input class="checkbox" id='fastentag' type="checkbox" name="fastentag" value="true">
                </div>
            </div>

            <div class="form-group optional">
                <label class="col-sm-3 control-label" for="futter">Futter:</label>
                <div class="col-sm-6">
                    <select class="form-control" id="futter" name="futter" size="1" ><?php echo $FutterDropDown; ?></select>
                </div>
            </div>

            <div class="form-group optional">
                <label class="col-sm-3 control-label" for="menge">Menge:</label>
                <div class="col-sm-6">
                    <input class="form-control" id="menge" type="number" min="1" max="100" step="1" name="menge" >
                </div>
            </div>

            <div class="form-group optional">
                <label class="col-sm-3 control-label" for="vitamine">Vitamine?</label>
                <div class="col-sm-6">
                    <input class="checkbox" type="checkbox" id="vitamine" name="vitamine" value="true">
                </div>
            </div>

            <div class="form-group optional">
                <label class="col-sm-3 control-label" for="calcium">Calcium:</label>
                <div class="col-sm-6">
                    <input class="checkbox" type="checkbox" id="calcium" name="calcium" value="true">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="bemerkung">Bemerkung:</label>
                <div class="col-sm-6">
                    <textarea class="form-control" id="bemerkung" name="bemerkung" cols="23" rows="3"></textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="submit"></label>
                <div class="col-sm-6">
                    <button class="btn btn-default" id="submit" type="submit" name="submit" value="true">Hinzufügen</button>
                </div>
            </div>

            <div class="form-group">
                <span style="color: red; "><?php echo $error_mes; ?></span>
                <span style="color: green; "><?php echo $success_mes; ?></span>
            </div>

        </form>
</div>
<div class="col-md-6">
    <h3>Fütterung:</h3>
    <table class="table-striped table-condensed table-hover">
        <thead>
        <tr>
            <th>Datum</th>
            <th>Futter</th>
            <th>Menge</th>
            <th>Vitamine</th>
            <th>Calcium</th>
            <th>Fastentag</th>
            <th>Bemerkung</th>
        </tr>
        </thead>
        <tbody>
            <?php echo $Fütterungen; ?>
        </tbody>
    </table>
</div>
</body>
</html>
