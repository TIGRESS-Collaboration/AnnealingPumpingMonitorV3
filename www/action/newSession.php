<?php
#The overall purpose of the script is to start a new annealing session.
#This includes killing the current logger, backing up and deleting datalog files,
#and finally restarting a brand new datalogging session.


chdir("/home/tigress/site/www");


#If logger is running kill the program
exec("sudo -u tigress pkill -f 'checkUptime.py'", $out1, $pyexit);
    

chdir("/home/tigress/site/www/bin");

exec("sudo -u tigress killall ./readDatacpp", $out2, $rdexit); 

$checkKill = exec("sudo -u tigress screen -ls | awk '/\.pyscript\t/ {print strtonum($1)}'");
#changed $checkKill != 0 to $checkKill != "" as looks as though checkKill returns nothing not zero if no pyscript screen session is running
if($checkKill != "") {
    exit("Failed to kill python");
}

exec("whoami 1>&2");
#$output = NULL;

#Now backup and delete datalog.csv and STD/Mean.csv
chdir("/home/tigress/site/www/datalogs");
exec("ls 1>&2");
exec("pwd 1>&2");
if(file_exists("datalog.csv")) {
    echo "datalog.csv exists";
    exec("cp --backup datalog.csv /home/tigress/site/www/datalogs/backupLogs", $out, $bupResult);
    if ($bupResult != 0) {
        exit("Error: failed to create backup of datalog.csv, exiting...");
    }
    chdir("/home/tigress/site/www/datalogs/backupLogs");
    $fileName = "datalogBackup" . date("Y_m_d_h_i_sa");
    exec("rename 's/datalog/$fileName/' datalog.csv");

    chdir("/home/tigress/site/www/datalogs");
    exec("sudo -u tigress rm datalog.csv", $outDelete, $statusDelete);
    if ($statusDelete != 0) {
        exit("error file not deleted");
    }else {
        echo "file deleted. ";
    }
}

if(file_exists("STDeviation.csv")) {
    exec("cp --backup STDeviation.csv /home/tigress/site/www/datalogs/backupLogs", $out, $bupResult);
    if ($bupResultS != 0) {
        exit("Error: failed to create backup of STDeviation.csv, exiting...");
    }

    chdir("/home/tigress/site/www/datalogs/backupLogs");
    $fileNameS = "stdBackup" . date("Y_m_d_h_i_sa");
    exec("rename 's/STDeviation/$fileNameS/' STDeviation.csv");

    chdir("/home/tigress/site/www/datalogs");
    exec("rm STDeviation.csv", $outDelete, $statusDelete);
    if ($statusDelete != 0) {
        exit("error file not deleted");
    }else {
        echo "file deleted. ";
    }

}


#Restart readData and pyscript
#exec("sudo -u tigress screen -d -m -S readData bash -c './readDatacpp'", $pholder, $restartRDcpp);

chdir("/home/tigress/site/www"); #Maybe go back to site to kill python, not sure
exec("sudo -u tigress screen -d -m -S pyscript bash -c 'python checkUptime.py'");

sleep(5);

$checkRestart = exec("sudo -u tigress screen -ls | awk '/\.pyscript\t/ {print strtonum($1)}'");
if ($checkRestart == 0) {
    exit("failed to restart logger");
}

$checkRDRestart = exec("sudo -u tigress screen -ls | awk '/\.readData\t/ {print strtonum($1)}'");
if ($checkRDRestart == 0) {
    exit("Failed to restart logger. Please reboot");
    #This should exit with an error message
    #The reboot php code 
}

echo "restart done";
?>
