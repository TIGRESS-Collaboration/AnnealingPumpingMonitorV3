<?php

  $fileArray = array();
  
  $dateArray = $_POST['dateArray'];
  $startMonth = $_POST['sMonth'];
  $endMonth = $_POST['eMonth'];
  
  //echo json_encode($dateArray);

  $startDate = date("Ymd", strtotime($dateArray[0]));
  $endDate = date("Ymd", strtotime($dateArray[1]));
   
  //echo $startDate;
  

  chdir("/home/tigress/site/www");


  $flag = 0;
  foreach(glob('/home/tigress/site/www/datalogs/datalog_*_*.csv') as $filename){
    
    $filename = basename($filename);
		$fileDate = date("Ymd", strtotime(explode('_', $filename)[1]));

    if(($fileDate >= $startDate) && ($fileDate <= $endDate)) {
      array_push($fileArray, $filename);
    }
      
  }
  

  chdir("/home/tigress/site/www/datalogs");

  exec("rm plotFile.csv");
  
  $stringOfFiles = implode(' ', $fileArray);
  //$command = "cat " . $stringOfFiles . " > plotFile.csv";
  
  exec("cat " . $stringOfFiles . " > plotFile.csv");



  //echo json_encode($stringOfFiles);

  //echo json_encode($command);



?>
