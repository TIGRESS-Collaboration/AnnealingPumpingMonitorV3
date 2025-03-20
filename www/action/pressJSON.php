<?php
#Take ion gauge calibration form input and save to the config.json file


if (isset($_POST['ionConvConst1'])) {
    $exp = strip_tags($_POST['ionConvConst1']);
    $gain = strip_tags($_POST['ionConvConst2']);
    $offset = strip_tags($_POST['ionConvConst3']);
    $curTime = date("Y-m-d h:i:s");

    $array = array("expFactor"=>$exp,"gain"=>$gain,"offset"=>$offset);
    $timeArray = array("calibTime"=>$curTime);
    
    $json = json_encode($array);
    $timeJson = json_encode($timeArray);
    
    $successStatus = 0;
    
    if(file_put_contents("/home/sharedjson/jsonFiles/ionConst.json", $json) === FALSE) {
        $successStatus = -1;
    }
    if(file_put_contents("/home/sharedjson/jsonFiles/calibTime.json", $timeJson) === FALSE) {
        $successStatus = -1;
    }
    echo $successStatus;
  }
  
  ?>
