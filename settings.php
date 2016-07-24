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
        $status = utf8_encode("&#10004");
        $status_check = 'checked';
    } else {
        $status = utf8_encode("&#10006");
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
<div class="col-md-4 form-group">
    <h3>Einstellungen:</h3>
    <form class="form-horizontal" action="settings.php" method="post">

        <div class="form-group">
            <label class="col-sm-7 control-label" for="time1" >Aktueller Sonnenaufgang:</label>
            <div class="col-sm-5">
                <input class="form-control" id="time1" type="time" name="sunrise" value="<?php echo $sunrise; ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-7 control-label" for="time2" >Aktueller Sonnenuntergang:</label>
            <div class="col-sm-5">
                <input class="form-control" id="time2" type="time" name="sunset" value="<?php echo $sunset; ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-7 control-label" for="groesse1" >Lüfter einschalten bei über:</label>
            <div class="col-sm-5">
                <input class="form-control" id="groesse groesse1" type="number" name="max" min="20" max="50" step="0.1" value="<?php echo $max; ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-7 control-label" for="groesse2" >Lüfter ausschalten bei unter:</label>
            <div class="col-sm-5">
                <input class="form-control" id="groesse groesse2" type="number" name="min" min="20" max="50" step="0.1" value="<?php echo $min; ?>">
            </div>
        </div>

        <div class="form-group">
            <ul class='tg-list'>
                <label class="col-sm-7 control-label tgl-btn" for="tb1" >Lüfter Automod:</label>
                <div class="col-sm-5">
                    <input class='tgl tgl-light' id='tb1' name="auto_mod" type='checkbox' <?php echo $auto_mod_check; ?>>
                    <label class='tgl-btn' for='tb1'></label>
                </div>
            </ul>
        </div>

        <div class="form-group" id="manuel">
            <ul class='tg-list'>
                <label class="col-sm-7 control-label tgl-btn" for="tb2" >Lüfter Manuell Schalten:</label>
                <div class="col-sm-5">
                    <input class='tgl tgl-light' id='tb2' name="status" type='checkbox' <?php echo $status_check; ?>>
                    <label class='tgl-btn' for='tb2'></label>
                </div>
            </ul>
        </div>

        <div class="form-group">
            <label class="col-sm-7 control-label" >Lüfter Status:</label>
            <div class="col-sm-5">
                <?php echo $status; ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-7 control-label" for="submit"></label>
            <div class="col-sm-5">
                <button class="btn btn-default" id="submit" type="submit" name="submit" value="true">Ändern</button>
            </div>
        </div>

        <div class="form-group">
            <span style="color: red; "><?php echo $error_mes1; ?></span>
            <span style="color: green; "><?php echo $success_mes1; ?></span>
        </div>

    </form>
</div>
<div class="col-md-6">
    <h3>Passwort ändern:</h3>
    <form class="form-horizontal" action="settings.php" method="post">

        <div class="form-group optional">
            <label class="col-sm-4 control-label" for="passwordAlt">Aktuelles Passwort:</label>
            <div class="col-sm-5">
                <input class="form-control" id="passwordAlt" type="password" name="passwordAlt" size="15" maxlength="40" required>
            </div>
        </div>

        <div class="form-group optional">
            <label class="col-sm-4 control-label" for="passwordNeu1">Neues Passwort:</label>
            <div class="col-sm-5">
                <input class="form-control" id="passwordNeu1" type="password" name="passwordNeu1" size="15" maxlength="40" required>
            </div>
        </div>

        <div class="form-group optional">
            <label class="col-sm-4 control-label" for="passwordNeu2">Neues Passwort nochmal:</label>
            <div class="col-sm-5">
                <input class="form-control" id="passwordNeu2" type="password" name="passwordNeu2" size="15" maxlength="40" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4 control-label" for="submitChangePassword"></label>
            <div class="col-sm-5">
                <button class="btn btn-default" id="submitChangePassword" type="submit" name="submitChangePassword" value="true">Ändern und neu einloggen</button>
            </div>
        </div>

        <div class="form-group">
            <span style="color: red; "><?php echo $error_mes2; ?></span>
        </div>

    </form>

    <h3 style="margin-top: 2em">Neues Futtertier hinzufügen:</h3>
    <form class="form-horizontal" action="settings.php" method="post">

        <div class="form-group optional">
            <label class="col-sm-4 control-label" for="foodName">Name:</label>
            <div class="col-sm-5">
                <input class="form-control" id="foodName" type="text" name="foodName" maxlength="40" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4 control-label" for="submitAddFood"></label>
            <div class="col-sm-5">
                <button class="btn btn-default" id="submitAddFood" type="submit" name="submitAddFood" value="true">Hinzufügen</button>
            </div>
        </div>

        <div class="form-group">
            <span style="color: red; "><?php echo $error_mes3; ?></span>
            <span style="color: green; "><?php echo $success_mes3; ?></span>
        </div>

    </form>

</div>
</body>
</html>