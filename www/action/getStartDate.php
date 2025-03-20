<?php

$file = fopen("/home/tigress/site/www/datalogs/datalog.csv", "r");

if(!feof($file)) {fgetcsv($file);}

$file_data = fgetcsv($file);

$date = $file_data[0];

echo $date;



?>
