<?php

$terra = new PDO('sqlite:../db/terra.sqlite');
$currentData = $terra->query('SELECT * FROM seven_day ORDER BY time DESC LIMIT 1',PDO::FETCH_ASSOC);
foreach ($currentData as $rowData) {
}

$currentSun = $terra->query('SELECT * FROM sun',PDO::FETCH_ASSOC);
foreach ($currentSun as $rowSun) {
}

$currentSettings= $terra->query('SELECT * FROM luefter',PDO::FETCH_ASSOC);
foreach ($currentSettings as $rowSettings) {
}


print(json_encode(array_merge(array_merge($rowData,$rowSun),$rowSettings)));




