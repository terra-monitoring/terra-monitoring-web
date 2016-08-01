<?php
$terra = new PDO('sqlite:../db/terra.sqlite');
$einstein = new PDO('sqlite:../db/einstein.sqlite');


if($_POST['action'] == 'get'){
    $currentData = $terra->query('SELECT * FROM seven_day ORDER BY time DESC LIMIT 1',PDO::FETCH_ASSOC);
    foreach ($currentData as $rowData) {
    }

    $currentSun = $terra->query('SELECT * FROM sun',PDO::FETCH_ASSOC);
    foreach ($currentSun as $rowSun) {
    }

    $currentSettings= $terra->query('SELECT * FROM luefter',PDO::FETCH_ASSOC);
    foreach ($currentSettings as $rowSettings) {
    }

    $food = "null,";

    $getFood= $einstein->query('SELECT * FROM food',PDO::FETCH_ASSOC);
    foreach ($getFood as $rowFood) {
        $food .= $rowFood[name] . "," ;
    }


    $array0 = array_merge($rowData,$rowSun);
    $array1 = array_merge($array0, $rowSettings);
    $array1[food] = $food;


    print(json_encode($array1));
} elseif ($_POST['action'] == "set") {
    $imeiPhone = $_POST['imei'];

    $login = $terra->query('SELECT * FROM login');
    foreach ($login as $row) {
        $algo = $row['salt'];
        $imei = $row['IMEI'];
    }


    if ($imei != hash($algo, $imeiPhone)){
        $message = "Dieses Mobil Phone ist nicht berechtigt!";
        print(json_encode($message));
        exit;
    }
    



    if ($_POST['page'] == "feed") {
        $dateUnformatted = $_POST['date'];
        $date = date("Y-m-d", strtotime($dateUnformatted));
        $fasting = $_POST['fastentag'];
        $food = $_POST['futter'];
        $amount =  $_POST['menge'];
        $vitamins = $_POST['vitamine'];
        $calcium = $_POST['calcium'];
        $comment = $_POST['bemerkung'];

        //check if date alrady there
        $last_entry = $einstein->query('SELECT date FROM feed');
        foreach ($last_entry as $rowDateCheck) {
            if($date == $rowDateCheck['date']){
                $dateCheck = true;
            }
        }
        if ($dateCheck == false) {
            if ($fasting == 'true') {
                $einstein->exec("INSERT INTO feed(date, Fastentag, Bemerkungen) VALUES('$date', 'true', '$comment')");

                //data check
                $last_insert = $einstein->query('SELECT date FROM feed');
                foreach ($last_insert as $row) {
                    $last_date = $row['date'];
                }
                if ($date = $last_date) {
                    $message = "Die Messwerte wurden erfolgreich hinzugefügt.";
                } else {
                    $message = "Das Hinzufügen hat nicht funktioniert!";
                }
            } elseif ($fasting == 'false') {

                $getFoodID = $einstein->query("SELECT * FROM food");
                foreach ($getFoodID as $row) {
                    if ($food == $row['name'])
                        $foodID = $row['id'];
                }

                $einstein->exec("INSERT INTO feed VALUES('$date', '$foodID', '$amount', '$vitamins', '$calcium', 'false', '$comment')");

                //data check
                $last_insert = $einstein->query('SELECT date FROM feed');
                foreach ($last_insert as $row) {
                    $last_date = $row['date'];
                }
                if ($date = $last_date) {
                    $message = "Die Messwerte wurden erfolgreich hinzugefügt.";
                } else {
                    $message = "Das Hinzufügen hat nicht funktioniert!";
                }
            } else {
                $message = "fatal error";
            }
        } else {
            $message = "Das Datum ist bereits vorhanden!";
        }


    } elseif ($_POST['page'] == "size"){
        $dateUnformatted = $_POST['date'];
        $date = date("Y-m-d", strtotime($dateUnformatted));
        $weight = $_POST['gewicht'];
        $length = $_POST['laenge'];

        //check if date alrady there
        $last_entry = $einstein->query('SELECT date FROM size');
        foreach ($last_entry as $rowDateCheck) {
            if($date == $rowDateCheck['date']){
                $dateCheck = true;
            }
        }
        if ($dateCheck == false) {
            $einstein->exec("INSERT INTO size VALUES('$date', $length, $weight)");

            //data check
            $last_insert = $einstein->query('SELECT date FROM size');
            foreach ($last_insert as $row) {
                $last_date = $row['date'];
            }
            if($date = $last_date){
                $message = "Die Messwerte wurden erfolgreich hinzugefügt.";
            } else {
                $message = "Das Hinzufügen hat nicht funktioniert!";
            }
        } else {
            $message = "Das Datum ist bereits vorhanden!";
        }

    } elseif ($_POST['page'] == "sun") {
        $sunrise = $_POST['sunrise'];
        $sunset = $_POST['sunset'];

        if ($sunrise < $sunset){
            $terra->exec("UPDATE sun SET sunrise = '$sunrise'");
            $terra->exec("UPDATE sun SET sunset = '$sunset'");

            //check sun
            $check_sun = $terra->query('SELECT * FROM sun');
            foreach ($check_sun as $row) {
                if ($sunrise == $row['sunrise'] && $sunset == $row['sunset']){
                    $message = "Das Speichern war erfolgreich.";
                } else {
                    $message = "Das Speichern hat nicht funktioniert!";
                }
            }

        } else {
            $message = "Sonnenaufgang muss vor dem Sonnenuntergang sein!";
        }

    } elseif ($_POST['page'] == "luefter"){
        $autoMod = $_POST['automod'];
        $manuel = $_POST['manuel'];
        $max = $_POST['max'];
        $min = $_POST['min'];

        if($max > $min){
            //set max and min
            $terra->exec("UPDATE luefter SET max = '$max'");
            $terra->exec("UPDATE luefter SET min = '$min'");


            //set Lüfter
            if ($autoMod == 'true' && $manuel == 'true'){
                $message = "Error";
            } else {
                if($autoMod == 'true') {
                    $terra->exec("UPDATE luefter SET auto_mod = 'true'");
                }   else {
                    $terra->exec("UPDATE luefter SET auto_mod = 'false'");
                }

                if($manuel == 'true') {
                    $terra->exec("UPDATE luefter SET status = 'true'");
                } else {
                    $terra->exec("UPDATE luefter SET status = 'false'");
                }
            }



            //check luefter
            $check_sun = $terra->query('SELECT * FROM luefter');
            foreach ($check_sun as $row) {
                if ($max == $row['max'] && $min == $row['min'] && $autoMod == $row['auto_mod'] && $manuel == $row['status']){
                    $message = "Das Speichern war erfolgreich.";
                } else {
                    $message = "Das Speichern hat nicht funktioniert!";
                }
            }

        } else {
            $message = "Der Obere Schaltgrenze muss höher sein als die Untere Schaltgrenze!";
        }


        
        

        
    }






    print(json_encode($message));
}







