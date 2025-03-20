<?php
#Returns the nubmer of lines in datalog.csv
chdir("/home/tigress/site/www/datalogs");
$string = shell_exec("wc -l datalog.csv");
$array = explode(" ",$string);
echo $array[0];
?>
