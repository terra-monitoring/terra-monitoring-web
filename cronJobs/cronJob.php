<?php
/*
 * cronJob.php is a cron Job
 * it runs every day on 00:15 am one time automatically
 */

//connection to database
$terra = new PDO('sqlite:../db/terra.sqlite');

//get the current times when lamps are on
$sun = $terra->query('SELECT * FROM sun');
foreach ($sun as $row) {
    $sunrise = $row['sunrise'] ;
    $sunset = $row['sunset'] ;
}

// get the day before to do all determination of the last before
$last_day= date('Y-m-d', strtotime("-1 days"));

// build a string in shape of the entries in the database
$current_sunrise.= $last_day . ' ' . $sunrise . '%';
$current_sunset.= $last_day . ' ' . $sunset . '%';


// get the maximum, minimum and average from the sensor temperature (on top) and write this results to the table s1
$s1 = $terra->query("select max(s1), min(s1), avg(s1) from seven_day where (s1>0) AND (time between '$current_sunrise' AND '$current_sunset')");
foreach ($s1 as $row) {
    $max_s1 = $row['max(s1)'];
    $min_s1 = $row['min(s1)'];
    $avg_s1 = round($row['avg(s1)'], 3);
}
$terra->exec("INSERT INTO s1 VALUES ('$last_day','$max_s1','$min_s1','$avg_s1');");


// get the maximum, minimum and average from the sensor temperature (in middle) and write this results to the table s2
$s2 = $terra->query("select max(s2), min(s2), avg(s2) from seven_day where (s2>0) AND (time between '$current_sunrise' AND '$current_sunset')");
foreach ($s2 as $row) {
    $max_s2 = $row['max(s2)'];
    $min_s2 = $row['min(s2)'];
    $avg_s2 = round($row['avg(s2)'], 3);
}
$terra->exec("INSERT INTO s2 VALUES ('$last_day','$max_s2','$min_s2','$avg_s2');");


// get the maximum, minimum and average from the sensor temperature (on bottom) and write this results to the table s3
$s3 = $terra->query("select max(s3), min(s3), avg(s3) from seven_day where (s3>0) AND (time between '$current_sunrise' AND '$current_sunset')");
foreach ($s3 as $row) {
    $max_s3 = $row['max(s3)'];
    $min_s3 = $row['min(s3)'];
    $avg_s3 = round($row['avg(s3)'], 3);
}
$terra->exec("INSERT INTO s3 VALUES ('$last_day','$max_s3','$min_s3','$avg_s3');");


// get the maximum, minimum and average from the sensor humidity and write this results to the table s4
$s4 = $terra->query("select max(s4), min(s4), avg(s4) from seven_day where (s4>0) AND (s4<100) AND (time between '$current_sunrise' AND '$current_sunset')");
foreach ($s4 as $row) {
    $max_s4 = $row['max(s4)'];
    $min_s4 = $row['min(s4)'];
    $avg_s4 = round($row['avg(s4)'], 3);
}
$terra->exec("INSERT INTO s4 VALUES ('$last_day','$max_s4','$min_s4','$avg_s4');");

// delete the day from "seven_day" table which is older then 6 days
$delete_day= date('Y-m-d', strtotime("-7 days"));
$delete="" . $delete_day . "%";
$terra->exec("DELETE FROM seven_day WHERE time LIKE '$delete'");

//disconnect database
$terra = NULL;