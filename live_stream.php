<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title>Terrarien Überwachung</title>
        
        <link rel="stylesheet" href="css/style.css">
        
    </head>
    <body>
<?php
SESSION_START();
include "menu/nav.html"; 
?>
<div class="col-md-12">
        <h3>Livestream</h3>
        <!--connection do livecam-->
        <img src="http://pi-terra.ddnss.de:8080?action=stream" />
</div>
</body>
</html>
