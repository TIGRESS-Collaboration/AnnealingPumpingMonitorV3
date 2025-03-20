<?php

  chdir("/home/tigress/site/www/datalogs/");
  //exec("rm plotFile.csv");
  
  $dateArray = array();
  chdir("/home/tigress/site/www/");

  
  foreach(glob('/home/tigress/site/www/datalogs/datalog_*_*.csv') as $filename){
    $filename = basename($filename);
    
		$fileDate = explode('_', $filename)[1];
    array_push($dateArray, $fileDate);
  }

  echo json_encode($dateArray);

?>
