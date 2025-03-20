
<?php

#Kills and restarts the datalogger.
#The "datalogger" includes checkUptime.py and readData.cpp
#Both will be restarted in respective screen sessions

chdir("/home/tigress/site/www"); #Maybe go back to site to kill python, not sure


#kill logger and python and UUGearDaemon

exec("sudo -u tigress pkill -f 'checkUptime.py'", $outputPy, $exitStatusPy);

chdir("/home/tigress/site/www/bin");
exec("sudo -u tigress killall ./readDatacpp", $output, $exitStatus);

#Restart readData and pyscript

chdir("/home/tigress/site/www"); #Maybe go back to site to kill python, not sure
exec("sudo -u tigress screen -d -m -S pyscript bash -c 'python checkUptime.py'");



sleep(5);


#Below code attempts to auto fix UUGear connection problems
$restartSuccess = FALSE;

$checkRestart = shell_exec("sudo -u tigress screen -ls | awk '/\.pyscript\t/ {print strtonum($1)}'");
$checkRDRestart = shell_exec("sudo -u tigress screen -ls | awk '/\.readData\t/ {print strtonum($1)}'");

if ($checkRestart == "") {
    exit("Failed to run pyscript");
}

if ($checkRDRestart == "") {
   exit("Failed to restart readData");

} else {
   $restartSuccess = TRUE;
}
$count = 0;

#Below was an attempt at a solution to connection problems (no longer in use)
while (($restartSuccess == FALSE) && $count < 5) {
   exec("sudo -u tigress screen -d -m -S readData bash -c './readDatacpp'");
   sleep(5);
   $checkRDRestart = shell_exec("sudo -u tigress screen -ls | awk '/\.readData\t/ {print strtonum($1)}'");

   if ($checkRDRestart != 0) {
      $restartSuccess = TRUE;
   }
   $count = $count + 1;
}

echo "Restart Done";

?>
