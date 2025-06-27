<?php
    $conn = mysqli_connect("localhost","root","","gestion_sante");
    if(!($conn)){
        echo "Connection echoué";
    }
?>