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
            if ($('#tb1').is(':checked')) {
                $("#manuel").hide();
            }
            $("#tb1").click(function () {
                $("#manuel").toggle();
            });
        });
    </script>

</head>
<body>

<?php
include "menu/nav.html";
// error message
$error_mes = "";

define("setFan", true);

//connection to database
$terra = new PDO('sqlite:db/terra.sqlite');

// after push "Ändern" this if-query looks if something changed and changed in database
if ($_POST['password'] == 'terra5#') {
    if (!empty($_POST['sunrise'])) {
        if ($_POST['sunrise'] < $_POST['sunset']) {
            $new_sunrise = $_POST['sunrise'];
            $terra->exec("UPDATE sun SET sunrise = '$new_sunrise'");
        } else {
            $error_mes = "Sonnenaufgang muss vor dem Sonnenuntergang sein!";
        }
    }

    if (!empty($_POST['sunset'])) {
        if ($_POST['sunset'] > $_POST['sunrise']) {
            $new_sunset = $_POST['sunset'];
            $terra->exec("UPDATE sun SET sunset = '$new_sunset'");
        } else {
            $error_mes = "Sonnenaufgang muss vor dem Sonnenuntergang sein!";
        }
    }

    if (!empty($_POST['max'])) {
        if ($_POST['max'] > $_POST['min']) {
            $new_max = $_POST['max'];
            $terra->exec("UPDATE luefter SET max = '$new_max'");
        } else {
            $error_mes = "Der Obere Schaltgrenze muss höher sein als die Untere Schaltgrenze!";
        }
    }

    if (!empty($_POST['min'])) {
        if ($_POST['min'] < $_POST['max']) {
            $new_min = $_POST['min'];
            $terra->exec("UPDATE luefter SET min = '$new_min'");
        } else {
            $error_mes = "Der Obere Schaltgrenze muss höher sein als die Untere Schaltgrenze!";
        }
    }


    if (isset($_POST['auto_mod']) && $_POST['auto_mod'] == true) {
        $terra->exec("UPDATE luefter SET auto_mod = 'true'");
    } else {
        $terra->exec("UPDATE luefter SET auto_mod = 'false'");
    }

    if (isset($_POST['status']) && $_POST['status'] == true) {
        $terra->exec("UPDATE luefter SET status = 'true'");
    } else {
        $terra->exec("UPDATE luefter SET status = 'false'");
    }
    //after all changes the script fanControl is running to check the current fan status
    require "cronJobs/fanControl.php";

} elseif (!empty($_POST['password'])) {
    $error_mes = "falsches Passwort!";
}

// get the current times when lamps are on
$current_sun = $terra->query('SELECT * FROM sun');
foreach ($current_sun as $row) {
    $sunrise = $row['sunrise'];
    $sunset = $row['sunset'];
}

// get the fan settings, auto-mod and status
$current_luefter = $terra->query('SELECT * FROM luefter');
foreach ($current_luefter as $row) {
    $max = $row['max'];
    $min = $row['min'];

    if ($row['auto_mod'] == 'true') {
        $auto_mod = 'an';
        $auto_mod_check = 'checked';
    } else {
        $auto_mod = 'aus';
        $auto_mod_check = 'unchecked';
    }

    if ($row['status'] == 'true') {
        $status = 'an';
        $status_check = 'checked';
    } else {
        $status = 'aus';
        $status_check = 'unchecked';;
    }
}

//disconnect database
$terra = NULL;
?>
<h3>Settings:</h3>
<form action="settings.php" method="post">
    <table>
        <tbody>
        <tr>
            <td style="width: 200px">Aktueller Sonnenaufgang:</td>
            <td>
                <input type="time" name="sunrise" value="<?php echo $sunrise; ?>">
            </td>
            <td>Uhr</td>
        </tr>
        <tr>
            <td>Aktueller Sonnenuntergang:</td>
            <td>
                <input type="time" name="sunset" value="<?php echo $sunset; ?>">
            </td>
            <td>Uhr</td>
        </tr>
        <tr>
            <td>Lüfter einschalten bei über:</td>
            <td>
                <input id="groesse" type="number" name="max" min="20" max="50" step="0.1" value="<?php echo $max; ?>">
            </td>
            <td>°C</td>
        </tr>
        <tr>
            <td>Lüfter ausschalten bei unter:</td>
            <td>
                <input id="groesse" type="number" name="min" min="20" max="50" step="0.1" value="<?php echo $min; ?>">
            </td>
            <td>°C</td>
        </tr>
        <tr>
            <td>Lüfter Automod:</td>
            <td>
                <ul class='tg-list'>
                    <input class='tgl tgl-light' id='tb1' name="auto_mod" type='checkbox' <?php echo $auto_mod_check; ?>>
                    <label class='tgl-btn' for='tb1'></label>
                </ul>
            </td>
        </tr>
        <tr>
            <td>Lüfter Manuell Schalten:</td>
            <td>
                <ul class='tg-list' id="manuel">
                    <input class='tgl tgl-light' name="status" id='tb2' type='checkbox' <?php echo $status_check; ?>>
                    <label class='tgl-btn' for='tb2'></label>
                </ul>
            </td>
        </tr>
        <tr>
            <td>Lüfter Status:</td>
            <td><?php echo $status; ?></td>
        </tr>
        </tbody>
    </table>
    <p>
        <label for="passwd">Passwort:</label>
        <input type="password" id="passwd" name="password" size="14" maxlength="40" required>
    <p>
       <span style="color: red; "><?php echo $error_mes; ?></span>
    <p>
        <button type="submit">Ändern</button>
</form>
</body>
</html>