#!/bin/bash

cd /home/tigress/site/www

sudo -u tigress pkill -f 'checkUptime.py'
#python script which starts readDatacpp and monitors its progress
cd /home/tigress/site/www 

sudo -u tigress screen -d -m -S pyscript bash -c "python checkUptime.py"
