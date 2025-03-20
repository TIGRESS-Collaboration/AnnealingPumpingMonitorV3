# TIGRESSAnnealingMonitor

This repo contains the source code for the Annealing Station Logger located at TRIUMF.


If you have any questions or problems with the code please email: jacksonfraser55@gmail.com

## General Overview

The datalogger setup is quite simple. The data that is fed to the raspberry pi by the arduino is analyzed in the readDatacpp function located in the src directory. This c++ file records all of the measurements, and is the centerpeice of the logger. The data can be seen from the web server hosted on the raspberry pi in plot and text form. readDatacpp will continuously run in the background and the current reading is updated every 4 seconds on the homepage using simple jQuery AJAX get requests. 

The buildcpp.sh file is used to recomple readData.cpp after changes have been made.

There was some issue with the logger timing out, so a solution was implemented in the form of checkUptime.py which is located in the ``` www/ ``` directory. This is a simple python script which checks that measurements are still being recorded every 5 minutes. If there are no new measurments then the python script will attempt to restart the logger.

The functionality of the buttons is all done using jQuery and php, with the php scripts being located in the ``` action/ ``` directory. The specific directory structure can be seen in the below directory section. 

When the datalogger is started, the readDatacpp and checkUptime.py will each be started in seperate detached screen sessions named readData and pyscript. If you are ssh'ed into the raspberry pi then you can attach to each of those screen sessions to view what is happening using the following commands:

```

screen -ls      This displays the screen sessions currently running.

screen -r readData    Attaches to the readData screen session where you can see program output

or 

screen -r pyscript   Attaches to the checkUptime.py screen session where you can see status messages (program output)

To detach from those sessions use CTRL-a d.

```
If the "reboot datalogger" or "start annealing session" buttons are failing to restart readDatacpp, first check if there is a screen session present with screen -ls. If none are present cd to cd /home/tigress/site/www/bin and try running ./readDatacpp. If it continues to fail run ./readDatacpp again. Sometimes the UUGear framework can be buggy, so you either need to wait for it to start working, or you can unplug and replug in the arduino/pi connection (this usually works).

Once readData is working again use CTRL-C to stop it and try the "Reboot Datalogger" button once again so that the proper screen sessions are initialized.

## Using the Site

The functionality of the site comes from the buttons which are discussed below. Many of these buttons are contained withing the settings menu.

### Buttons

#### Temperature and Pressure Plots / Download/Plot Legacy Files

Both of these buttons are fairly self explanatory. They redirect to pages where the plots of the data can be viewed, and csv datalog files can be downloaded. Plot legacy files allows you to plot older annealing sessions.

#### New Annealing Session

This button will stop the current annealing session, backup the datalog files, and restart a new annealing session.

This button is meant to easily start a new Annealing Session when another detector is hooked up to the bench. The datalog file will be deleted and backed up so only use this if you are switching out the detectors. Each part of the functionality of this button (stopping the logger, restarting the logger, backing up and saving files etc) is replicated in individual buttons below if you only want to do one thing at a time.

#### Edit Configuration

This allows the user to edit the text displayed on the page under: **Current Location** and **Currently Monitoring**. The **Info Last Updated** field will update automatically to the current time/date.

#### Recalibrate Temperature / Ion Gauge

These buttons allow the user to edit the calibration constants used in the readDatacpp program which is performing the datalogging. They will also automatically update the **Last Calibrated Date** on submit. 

**Note: When new calibration constants are submitted, the datalogger will be killed and restarted so as to register your changes**

#### Reboot / Stop Datalogger

The functionality of these buttons is fairly self explanatory. The Reboot button will kill and restart the logger, and Stop will kill the logger. The page will refresh on submission, and the logger status will take 4 seconds to update.

#### Clear Log Files

This button allows the user to delete and backup the datalog.csv file which is the data being plotted in temperature and pressure plots. 

The user is given the option to backup the datalog.csv file or just delete it with no backup. This choice is then followed by two confirmation messages to ensure that the file is not deleted unintentionally. 

#### Edit Sample Rate

Allows you to control how often data is saved to the datalog.csv file.

## Troubleshooting

### Find the Source of the Error

If you are unsure where exactly the error is occurring if, for example, one of the buttons on the homepage is not working, a good first place to check is the error log. error.log will contain a record of errors associated with php/ajax functionality of the site. This is located in the following directory if your setup is the same:

```
/home/tigress/site/logs
```

Even if your directory setup is slightly different, the ```/logs``` directory can clearly be seen in the root of this github repo. As long as you have linked this directory when setting up your nginx server then your error log should be working.


### Data Logger not Working

If readData.cpp does not start running after several tries using the reboot button, you can SSH into the pi to start debugging.



Once you are in first try running:
```
ps -au
```

This command will display a list of the currently running processes. If you do not see ./readDatacpp on that list then the program is not running.

If checkUptime.py is not there then both failed to restart. If this is the case, cd to:

```
cd /home/tigress/site/www/bin
```
Once in the bin run readData with the command:
```
./readDatacpp
```
If the output message says something about "UUGEar" not connecting then the fastest fix is probably to unplug and replug in the arduino to the raspberry pi. ALSO make sure that the correct UUGear ID is being used in readData.cpp. You determine the id for each arduino by using ./lsuu which in located in /www/bin. 



Now if you do 
```
ps -au
```
and readData or checkUptime.py DO appear on the list of programs run the following command to see current screen sessions:
```
screen -ls
```


Now depending on which sessions are up you can attach to the session to view the output / error messages to help you debug with the command:
```
screen -r readData OR screen -r pyscript
```

If these were started manually for what ever reason you will have to use the id of the screen session to reattach as they will not be specifically named readData or pyscript.

After looking at the program outputs, if it is not helpful use CTRL-C to quit kill ./readDatacpp and quit() or CTRL-C to kill checkUptime.

After this begin the steps described above where you attempt to restart the logger manually with ./readDatacpp.


##  Dependancies

If this web server is being imported to a new machine (ie. new raspberry pi etc) there are a few packages that need to be installed.

Install rename command.

Install gawp.

Install gawk?

Install screen.


File paths are discussed somewhat below, but www-data must have permission to write to JSON files.

You can create a directory somewhere where it already has permission or leave it in tigress home directory tree and give that folder 777 permissions or something along those lines.

Also when creating file structure for datalog things, do not create the backupLogs directory with tigress. You need to let www-data create it. To do so use the command:

```
sudo -u www-data mkdir backupLogs
```

You will also need to make modifications to the file in sites-avaliable within nginx:

```
/etc/nginx/sites-available/
```

This directory is where you set the root path of the site etc.

PHP has to be manually enabled in a new nginx setup. Fastcgi handles the php and it should be already installed on a pi with debian, or it comes with the nginx package.

Curretnly it is in the file default.



You will need to uncomment a few lines in here to enable php handling by fastCGI as well as adding index.php to a list and specifying the access and error log paths.

If anything on the site is initially not working check php script and ensure that all commands used have been imported. You can debug this by running everything manually from the terminal.



## Code Structure

The directory structure is as follows:

The root of the git hub repo correspongs to 

```
/home/tigress/site
```

The root of the sites files (ie. the directory that nginx uses to locate your index.php page etc) is:

```
/home/tigress/site/www
```

This contains all of the php/html/css/javascript for the project.

All php scripts are within the ``` /action ``` directory.

``` /datalogs ``` is a relative link that points to the directory where you want to store your csv files containing all of the recorded and backed up data. Under the current setup this is points to ```/home/tigress/mnt/EXFAT/datalogs/```

The following files will also need to be added as they contain parameters used for calculations and such. All following subdirectories need to give www-data permission to read/write to files.

In ```/home/sharedjson/jsonFiles/``` include the following files (again make sure they have permissions for user www-data):

calibTime.json

config.json

ionConst.json

plotConfig.json

samplerate.json

tcConst.json

tempConst.json



Once you make these new files, make sure you add values to them by submitting calibration forms of the site. If not readDatacpp will fail at runtime because json from the files will fail to parse.

Other Tips

To find Arduino serial number do ./lsuu while in the /bin directory: /home/tigress/site/www/bin.
This number should be put into the readData.cpp source code whenever a new arduino is used with your setup.
