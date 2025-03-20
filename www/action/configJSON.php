<?php
#Takes configuration form input from form located on index.php and saves it to json file.


if (isset($_POST['location'])) {
    $loc = strip_tags($_POST['location']);
    $mon = strip_tags($_POST['monitoring']);
    $curtime = date("Y-m-d h:i:s");

    $array = array("location"=>$loc,"monitoring"=>$mon,"time"=>$curtime);

    $json = json_encode($array);

    if(file_put_contents("/home/sharedjson/jsonFiles/config.json", $json) === FALSE) {
        echo "-1";
    } else {
        echo "0";
    }
  }
  ?>
