<?php
#Kills the logger

chdir("/home/tigress/site/www");
exec("sudo -u tigress pgrep -f 'python checkUptime.py'", $output, $pyexit);
if($pyexit == 0) {
exec("sudo -u tigress pkill -f 'checkUptime.py'", $outputPy, $exitStatusPy);
} else {
    $exitStatusPy = 0;
}

chdir("/home/tigress/site/www/bin");
exec("sudo -u tigress pgrep readData", $output, $rdexit);
if($rdexit == 0) {
    exec("sudo -u tigress killall ./readDatacpp", $output, $exitStatus);
} else {
    $exitStatus = 0;
}

#Check if logger was terminated successfully
if ($exitStatus != 0 || $exitStatusPy != 0) {
    exit("Failed to kill logger");
} else {
    echo "logger killed";
}

?>
