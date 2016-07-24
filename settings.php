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
SESSION_START();
include "menu/nav.html";
// error message
$error_mes1 = "";
$error_mes2 = "";
$error_mes3 = "";
// success message
$success_mes1 = "";
$success_mes3 = "";


define("setFan", true);

//connection to database
$terra = new PDO('sqlite:db/terra.sqlite');

// after push "Ändern" this if-query looks if something changed and changed in database
if ($_SESSION['login'] == 'true' && $_POST['submit'] == 'true') {
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
    $success_mes1 = "Einstellungen wurde Erfolgreich geändert!";
} elseif ($_POST['submit'] == 'true') {
    $error_mes1 = "Bitte erst einloggen!";
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

// Changing Password
if($_SESSION['login'] == 'true' && $_POST['submitChangePassword'] == 'true'){

    $login = $terra->query('SELECT * FROM login');
    foreach ($login as $row) {
        $algo = $row['salt'];
        $password = $row['password'];
    }

    if($password == hash($algo, $_POST['passwordAlt'])){
        if($_POST['passwordNeu1'] == $_POST['passwordNeu2']){
            $newPassword = $_POST['passwordNeu1'];

            $newPasswordHash = hash($algo, $newPassword);

            $terra->exec("UPDATE login SET password = '$newPasswordHash'");

            $_SESSION['login'] = "false";
            $_SESSION['logout'] = "true";
            header('location: login.php');
        } else {
            $error_mes2 = "Neues Password stimmt nicht überein!";
        }
    } else {
        $error_mes2 = "Aktuelles Password war Falsch!";
    }
} elseif ($_POST['submitChangePassword'] == 'true'){
    $error_mes2 = "Bitte erst einloggen!";
}


// Add new food
if($_SESSION['login'] == 'true' && $_POST['submitAddFood'] == 'true'){

    $foodName = $_POST['foodName'];
    //connection to database
    $einstein = new PDO('sqlite:db/einstein.sqlite');

    $countNr = $einstein->query('SELECT max(id) as maxID FROM food');
    foreach ($countNr as $row) {
        $nr = $row['maxID']+1;
    }

    $einstein->exec("INSERT INTO food VALUES($nr, '$foodName' )");

    $success_mes3 = "Erfolgreich Hinzugefügt!";
    //disconnect database
    $einstein = NULL;
} elseif ($_POST['submitAddFood'] == 'true'){
    $error_mes3 = "Bitte erst einloggen!";
}



//disconnect database
$terra = NULL;
?>
<div id="left_col">
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
            <tr id="manuel">
                <td>Lüfter Manuell Schalten:</td>
                <td>
                    <ul class='tg-list' >
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
            <button type="submit" name="submit" value="true">Ändern</button>
        <p>
            <span style="color: red; "><?php echo $error_mes1; ?></span>
            <span style="color: green; "><?php echo $success_mes1; ?></span>
    </form>
</div>
<div id="right_col">
    <h3>Passwort ändern:</h3>
    <form action="settings.php" method="post">
        <table>
            <tbody>
            <tr>
                <td style="width: 180px">Aktuelles Passwort:</td>
                <td>
                    <input type="password" name="passwordAlt" size="15" maxlength="40" required>
                </td>
            </tr>
            <tr>
                <td style="width: 180px">Neues Password:</td>
                <td>
                    <input type="password" name="passwordNeu1" size="15" maxlength="40" required>
                </td>
            </tr>
            <tr>
                <td style="width: 180px">Neues Passwort nochmal:</td>
                <td>
                    <input type="password" name="passwordNeu2" size="15" maxlength="40" required>
                </td>
            </tr>
            </tbody>
        </table>
        <p>
            <button type="submit" name="submitChangePassword" value="true">Ändern und neu einloggen</button>
        <p>
            <span style="color: red; "><?php echo $error_mes2; ?></span>
    </form>

    <h3 style="margin-top: 2em">Neues Futtertier hinzufügen:</h3>
    <form action="settings.php" method="post">
        <table>
            <tbody>
            <tr>
                <td style="width: 60px">Name:</td>
                <td>
                    <input type="text" name="foodName" size="20" maxlength="40" required>
                </td>
            </tr>
            </tbody>
        </table>
        <p>
            <button type="submit" name="submitAddFood" value="true">Hinzufügen</button>
        <p>
            <span style="color: red; "><?php echo $error_mes3; ?></span>
            <span style="color: green; "><?php echo $success_mes3; ?></span>
    </form>

</div>
</body>
</html>