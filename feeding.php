
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Terrarien Überwachung</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/toggleButton.css">

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
$result = $einstein->query('SELECT name FROM food');
foreach ($result as $row) {
    $FutterDropDown .= "<option>" . $row['name'] . "</option>";
}


// get all feeds
$resultFeed = $einstein->query('SELECT * FROM feed');
foreach ($resultFeed as $row) {
    $Fütterungen .= "<tr>
                        <td>" . date("d.m.Y", strtotime($row['date'])) . "</td>
                        <td>" . $row['foodID'] . "</td>
                        <td>" . $row['menge'] . "</td>
                        <td>" . $row['Vitamine'] . "</td>
                        <td>" . $row['Calcium'] . "</td>
                        <td>" . $row['Fastentag'] . "</td>
                        <td>" . $row['Bemerkungen'] . "</td>
                    </tr>";
}


// after push "Hinzufügen" this if-query put the new data in database
if ($_POST['password'] == 'terra5#') {
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

} elseif (!empty($_POST['password'])) {
    $error_mes = "falsches Passwort!";
}

//disconnect database
$terra = NULL;
?>
<div id="left_col">
    <h3>Neue Fütterung:</h3>
    <h4>(Letzter Eintrag am <?php echo $last_date;?>):</h4>
    <form action="feeding.php" method="post">
        <table>
            <tbody>
            <tr>
                <td style="width: 50px">Datum:</td>
                <td>
                    <input type="date" name="date" style="width: 130px">
                </td>
            </tr>
            <tr>
                <td>Fastentag?</td>
                <td>
                    <input id='fastentag' type="checkbox" name="fastentag">
                </td>
            </tr>
            <tr class="optional">
                <td>Futter:</td>
                <td>
                    <select name="futter" size="1" style="width: 130px">
                        <?php echo $FutterDropDown; ?>
                    </select>

                </td>
            </tr>
            <tr class="optional">
                <td>Menge:</td>
                <td>
                    <input type="number" min="1" max="100" step="1" name="menge" style="width: 130px">
                </td>
            </tr>
            <tr class="optional">
                <td>Vitamine?</td>
                <td>
                    <input type="checkbox" name="vitamine" >
                </td>
            </tr>
            <tr class="optional">
                <td>Calcium?</td>
                <td>
                    <input type="checkbox" name="calcium">
                </td>
            </tr>
            <tr>
                <td>Bemerkung:</td>
                <td>
                    <textarea name="bemerkung" cols="20" rows="3" ></textarea>
                </td>
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
</div>
<div id="right_col">
    <h3>Fütterung:</h3>
    <table>
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