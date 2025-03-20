
import time
import subprocess
from datetime import datetime


flag = 0;

while(True):

    #Get the most recent measurment performed by the logger
    inputFile = open("/home/tigress/site/www/datalogs/currentReading.txt", "r");
    line = inputFile.read();
    inputFile.close();
    now = datetime.now();

    #Retreiving and formatting date string
    slicer = slice(13, 32);
    lastTime = line[slicer];
    curTime = now.strftime("%Y/%m/%d %H:%M:%S");

    try:
        lastTimeD = datetime.strptime(lastTime, "%Y/%m/%d %H:%M:%S");
    except ValueError:
        print("CurrentReading file not initialized, restarting readData");
        flag = 0;

    curTimeD = datetime.strptime(curTime, "%Y/%m/%d %H:%M:%S");

    #The difference between current time and last datapoint
    dif = curTimeD - lastTimeD;
    difInSec = dif.total_seconds();

    if(difInSec > 30 or flag == 0):
        #need to restart
        print("Datalogger not running");
        subprocess.Popen(['sh', './restartLogger.sh']);
    else:
        print("logger running at:", curTime); #Not sure if this time print will work

    flag = 1;
    time.sleep(300);

