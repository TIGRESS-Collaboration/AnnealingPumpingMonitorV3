# PumpingMoniterV3

This repo contains updated code and documentation for the HPGe pumping station remote loggers in the tiglab.

## Function

The datalogger uses a function called readData.cpp to poll data from the arduino. The polling rate can be changed but is automatically set to 10s. Another function, checkUptime.py, ensures that the data logger continues to run. The data can be viewed on the website in plot and text form. The button functionality is done using php scripts in the action directory. The buttons are all fairly self-explanatory but are outlined clearly in the [previous version repository](https://github.com/TIGRESS-Collaboration/AnnealingPumpingMonitorVer2/blob/master/README.md). A more in depth explanation of the loggers functions can be found here as well.

## Setup instructions

### Dependencies

There are a few packages that must be installed when setting up a new raspberry pi.

1. rename command
2. gawk
3. screen
4. php-fpm (any version is fine, the most up to date is likely best, but make sure that you update this in the nginx default site configuration)
   
### Other software to download

The setup runs using nginx to host a local server and uses the UUGear library to interface with the arduino. The code contains the necessary parts of the UUGear library on the raspberry pi side, however [this sketch](https://github.com/uugear/UUGear/blob/master/Arduino/UUGear/UUGear.ino) must be uploaded to the arduino.

### Installation steps

1. Setup the raspberry pi. The default operating system and settings should be sufficient. Set the username to tigress.
2. In the tigress home directory install the zip file from this repository. Unzip the file and rename the directory to 'site'.
3. Install nginx into the etc directory. When installed, the localhost page should show a basic nginx welcome message.
4. Go to the sites-available directory and edit the default file. After this is done the local host page should show the site, but with little functionality.
  - Open the default file in the following directory:
     
```
cd etc/nginx/sites-available/
```
  - Change the root from the default to:
```
/home/tigress/site/www
```
  - Add index.php to a list, the default file should have a comment flagging when to do so
  - Uncomment a few lines of code enabling fastcgi with php-fpm (This is where you must input the correct php version that you have downloaded).
  - Change the access and error log paths from the default (In the current setup they are in the directory /home/tigress/site/logs). For the error log, set the warn level to error. This means adding the word error after the file path.
```
error_log /home/tigress/site/logs/error.log error;
access_log /home/tigress/site/logs/access.log;
```
5. Setup files and paths   
  - Create the backupLogs directory within the datalogs directory. www-data should do this:
```
cd home/tigress/site/www/datalogs/
sudo -u www-data mkdir backupLogs
```
  - Create shared json files in /home/sharedjson/jsonFiles and, ensure that www-data can read and write to these files
    - calibTime.json
    - config.json
    - ionConst.json
    - plotConfig.json
    - samplerate.json
    - tcConst.json
    - tempConst.json
   
  - Create datalogs.csv in the datalogs directory

6. Set up the arduino
   - Upload the [this sketch](https://github.com/uugear/UUGear/blob/master/Arduino/UUGear/UUGear.ino) to the arduino.
   - After connecting the arduino, navigate to the site bin directory and use the command ./lsuu to find the arduino serial number. Add this number into the readData.cpp source code.

7. Compile code and start running
   - Submit calibration files by clicking edit settings in the top right corner of the site. Submit calibrations to each of the recalibrate pages. This updates the associated json files. Without this, readData will fail at runtime as it will not be able to parse the json files.
   - buildcpp.sh is a script to recompile readData.cpp. This should be run prior to running the monitor for the first time.
   - startMonitor.sh is the script that starts checkUptime.py which in turn starts readData.cpp. Running this *should* start the logger. If the monitor has started properly, the command screen -ls should show pyscript and readData sessions.

8. Change the device node name and enable ssh
   - To run the raspberry pi as a headless device you must enable ssh. This may already be done. If not, ensure that you have generated hostkeys and that access is restricted such that only root can read and write (not execute). Also make sure that hostkeys have been uncommented in the ssh configuration files. This can all be found in the directory /etc/ssh/.
   - The device will likely have to be added to TRIUMF's iot. They will change the node name in accordance with our request.

## Troubleshooting

To confirm that the logger is running use the command ps -aux in the terminal. You should see processes running for both readData and checkUptime. readData or both readData and checkUptime are not there, you can try starting readData from the bin directory using the command ./readDatacpp. If the error message gives warnings about UUGear, you can try resetting the arduino and unplugging it and plugging it back in. If the problem persists, consider reuploading the UUGear sketch to the arduino.

If readData still refuses to start, ensure that the sharedJson files that were created in step 5 of setup are populated with the values you submitted to the website. It may also be worth checking that the directory structure is set up correctly, and that the functions are not trying to access any directories or files that don't exist.

If the functions are running but there are issues with site functionality, check the error log in the logs directory. Here, errors that have to do with the site functionality will be logged. 

If there seems to be some other issue, you can try viewing the functions of readData and checkUptime by viewing their screen sessions. The command screen -ls will show all current screen sessions. If readData or checkUptime is here, you should be able to view the session with screen -r readData or screen -r checkUptime.

It may also be worth looking at the two previous repositories [Version 1](https://github.com/TIGRESS-Collaboration/AnnealingPumpingMonitor), [Version 2](https://github.com/TIGRESS-Collaboration/AnnealingPumpingMonitorVer2/blob/master/README.md) to see if there are any hints there.




