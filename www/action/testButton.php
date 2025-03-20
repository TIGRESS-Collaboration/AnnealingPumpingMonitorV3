<?php
#Deletes and creates a backup of datalog.csv depending on prior user input.
#also kills and restarts the logger before and after the process.

chdir("/home/tigress/site/www");

#check if logger is running
#exec("pgrep 'readData'", $randOut1, $exit1);
#exec("pgrep -f 'python checkUptime.py'", $randOut2, $exit2);

#If logger is still running kill it

$pyexit = 0;
$rdexit = 0;
exec("sudo -u tigress pkill -f 'checkUptime.py'", $out1, $pyexit);


chdir("/home/tigress/site/www/bin");

exec("sudo -u tigress killall ./readDatacpp", $out2, $rdexit); #maybe use pgrep to make sure theyre all dead


$output = NULL;

chdir("/home/tigress/site");

#Backup/delete datalog.csv depending on users input
chdir("/home/tigress/site/www/datalogs");
if (file_exists("datalog.csv")) {
    if ($_POST['backUpBool'] == true) {
        exec("cp --backup datalog.csv /home/tigress/site/www/datalogs/backupLogs", $out, $bupResult);
        if ($bupResult != 0) {
            exit("Error: failed to create backup, cancelling delete");
        }
        chdir("/home/tigress/site/www/datalogs/backupLogs");
        $fileName = "datalogBackup" . date("Y_m_d_h_i_sa");
        exec("rename 's/datalog/$fileName/' datalog.csv");
        echo "here";
    }

    chdir("/home/tigress/site/www/datalogs");
    exec("rm datalog.csv", $outDelete, $statusDelete);
    if ($statusDelete != 0) {
        exit("error file not deleted");
    }else {
        echo "file deleted";
    }
}

#chdir("/home/tigress/site/www");

#exec("bash restartLogger.sh");
#exec("bash startpy.sh");

?>
