<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Terrarien Überwachung</title>
    
    <link rel="stylesheet" href="css/style.css">

    <!--google charts -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="js/overall.js"></script>
    

</head>
<body>

    <?php include "menu/nav.html"; ?>

    <h3>Gesamt:</h3>
    <h4>Temperatur Oben:</h4>
    <div id="linechart_material_s1" class="linechart"></div>
    <h4>Temperatur Mitte:</h4>
    <div id="linechart_material_s2" class="linechart"></div>
    <h4>Temperatur Unten:</h4>
    <div id="linechart_material_s3" class="linechart"></div>
    <h4>Luftfeuchtigkeit:</h4>
    <div id="linechart_material_s4" class="linechart"></div>

</body>
</html>