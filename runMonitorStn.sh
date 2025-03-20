
CURRENT_DIR=`readlink -f .`
echo "CURRENT_DIR = " $CURRENT_DIR

TARGET_DIR= $CURRENT_DIR/www/bin
service=nginx

# Ensure NGINX is running
echo "Ensuring NGINX web server is running..."

if (( $(ps -ef | grep -v grep | grep $service | wc -l) > 0 )); then
	echo "$service is running!!! No need to restart."
else
	echo "Error: NGINX not started. Launching service..."
	sudo /etc/init.d/$service restart
fi

# Incase this is a restart from a bad program halt...

killall UUGearDaemon

# Launch the data reading program

cd $TARGET_DIR && nohup ./readData > /dev/null 2>&1 &
echo "Checking is readData is running ok...please wait..."
sleep 30
while (!(pgrep "readData" > /dev/null))
    echo "readData script did not launch ok"
	echo "Restarting..."
	nohup ./readData > /dev/null 2>&1 &
	sleep 30
fi

echo "readData program launched ok!"
echo "Monitoring station startup successful! :D"
