<?php
#Take temperature calibration form input and save it to JSON file

    $tc2 = strip_tags($_POST['tc']);

    
    $array = array("tc"=>$tc2);
    
    $json = json_encode($array);
    
    
    
    if(file_put_contents("/home/sharedjson/jsonFiles/tcConst.json", $json) === FALSE) {
        echo "-1";
    } else {
        echo "0";
    }
?>