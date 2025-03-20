#!/bin/sh

cd /home/tigress/site/www/datalogs
cp --backup datalog.csv /home/tigress/site/www/datalogs/backupLogs
rm datalog.csv

#python script which starts readDatacpp and monitors its progress
cd /home/tigress/site/www 

sudo -u tigress screen -d -m -S pyscript bash -c "python checkUptime.py"




