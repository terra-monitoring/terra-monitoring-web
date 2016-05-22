<?php
SESSION_START();
$table = $_POST["table"];
$query= $_POST["query"];
$day = $_POST["day"];
$terra = new PDO('sqlite:../db/terra.sqlite');

if ($table=='daily') {
    $sun = $terra->query('SELECT * FROM sun');
    foreach ($sun as $row) {
        $sunrise = $row['sunrise'] ;
        $sunset = $row['sunset'] ;

    }
    if ($day=='true'){
        $current_date=$_SESSION['print_day'];
    } else {
        $current_date= date('Y-m-d');
    }
    $current_sunrise= $current_date . ' ' . $sunrise . '%';
    $current_sunset= $current_date . ' ' . $sunset . '%';


    print '{"cols": [
                    {"label":"","type":"string"},
                    {"label":"Luftfeuchtigkeit","type":"number"}
                  ],';

    print '"rows": [';

    $result = $terra->query("SELECT * FROM '$query'
                              WHERE (s4<100) AND (s4>0) AND (time BETWEEN '$current_sunrise' AND '$current_sunset')");

    foreach ($result as $row) {
        print '
                {"c":[
                    {"v":"' . date("H:i:s", strtotime($row['time'])) . '",},
                    {"v":' . $row['s4'] . ',}
                    ]
                },';

    }
    print ']}';


} elseif ($table=='total') {
    print '{"cols": [
                        {"label":"","type":"string"},
                        {"label":"Maximal","type":"number"},
                        {"label":"Minimal","type":"number"},
                        {"label":"Durchschnitt","type":"number"}
                      ],';

    print '"rows": [';

    $result = $terra->query("SELECT * FROM $query");

    foreach ($result as $row) {
        print '
                    {"c":[
                        {"v":"' . date("d.m.Y", strtotime($row['date'])) . '",},
                        {"v":' . $row['max'] . ',},
                        {"v":' . $row['min'] . ',},
                        {"v":' . $row['avg'] . ',}
                        ]
                    },';
    }
    print ']}';
}

$terra = NULL;