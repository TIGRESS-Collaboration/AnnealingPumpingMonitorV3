#!/bin/sh

cd /home/tigress/site/www/bin

echo "killing readDatacpp"

sudo -u tigress killall ./readDatacpp

echo "restarting readDatacpp"

until sudo -u tigress screen -d -m -S readData bash -c "./readDatacpp"
do 
    echo "restarting..."
    sleep 6
done

    
