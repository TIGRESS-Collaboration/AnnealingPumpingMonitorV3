<?php
#Take temperature calibration form input and save it to JSON file
if (isset($_POST['tempConvConst'])) {
    $conv = strip_tags($_POST['tempConvConst']);
    $off = strip_tags($_POST['tempOffConst']);
    $curTime = date("Y-m-d h:i:s");

    $array = array("conv"=>$conv,"offset"=>$off);
    $timeArray = array("calibTime"=>$curTime);

    $json = json_encode($array);
    $timeJson = json_encode($timeArray);
    
    $successStatus = 0;
    
    if(file_put_contents("/home/sharedjson/jsonFiles/tempConst.json", $json) === FALSE) {
        $successStatus = -1;
    }
    
    if(file_put_contents("/home/sharedjson/jsonFiles/calibTime.json", $timeJson) === FALSE) {
        $successStatus = -1;
    }
    echo $successStatus;
  }
  
  ?>
