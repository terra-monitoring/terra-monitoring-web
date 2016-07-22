<?php
$einstein = new PDO('sqlite:../db/einstein.sqlite');

print '{"cols": [
                {"label":"","type":"string"},
                {"label":"Gewicht [g]","type":"number"},
                {"label":"Länge [cm]","type":"number"}
              ],';

print '"rows": [';

$result = $einstein->query("SELECT * FROM size ");

foreach ($result as $row) {
    print '
            {"c":[
                {"v":"' . $row['date'] . '"},
                {"v":' . $row['weight'] . '},
                {"v":' . $row['length'] . '}
                ]
            },';
}
print ']}';



$terra = NULL;