<?php
#Take temperature calibration form input and save it to JSON file

    $PT100 = strip_tags($_POST['PT100']);
    $IG = strip_tags($_POST['IG']);
    $TC2 = strip_tags($_POST['TC2']);
    $STDev = strip_tags($_POST['STDev']);
    
    $array = array("PT100"=>$PT100,"IG"=>$IG,"TC2"=>$TC2,"STDev"=>$STDev);
    
    $json = json_encode($array);
    
    if(file_put_contents("/home/sharedjson/jsonFiles/plotConfig.json", $json) === FALSE) {
        echo "-1";
    } else {
        echo "0";
    }
?>