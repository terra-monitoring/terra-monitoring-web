<?php


//connection to database
if (defined("setFan")){
    $terra_db = new PDO('sqlite:db/terra.sqlite');
} else {
    $terra_db = new PDO('sqlite:../db/terra.sqlite');
}



// get the fan settings
$luefter_table = $terra_db->query("select * from luefter");
foreach ($luefter_table as $row) {
    $max_limit = $row['max'];
    $min_limit = $row['min'];
    $autoMod = $row['auto_mod'];
    $status = $row['status'];
}

// get the current temperature from the highest point
$luefter_table = $terra_db->query("select s1 from seven_day ORDER BY time  DESC LIMIT 1;");
foreach ($luefter_table as $row) {
    $current_temp = $row['s1'];
}

/*
 * check if is auto-mod on
 * if its on: it will compare the current temperature with the upper and lowest limit
 * and if required switch the fan on or off
 */
if ($autoMod == 'true' && $status == 'false' && $current_temp > $max_limit){
    $terra_db->exec("UPDATE luefter SET status = 'true' ;");
} elseif ($autoMod == 'true' && $status == 'true' && $current_temp < $min_limit) {
    $terra_db->exec("UPDATE luefter SET status = 'false' ;");
}

//disconnect database
$terra_db = NULL;