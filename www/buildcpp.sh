CURRENT_DIR=`readlink -f .`

echo "CURRENT_DIR = " $CURRENT_DIR

TARGET_DIR=$CURRENT_DIR/bin

rm -rf $TARGET_DIR

rm $TARGET_DIR/readDatacpp

mkdir $TARGET_DIR

echo "TARGET_DIR = " $TARGET_DIR

cd src/srcUUGear

echo "Building src..."

gcc  -o $TARGET_DIR/UUGearDaemon UUGearDaemon.c serial.c -lrt -lpthread
gcc  -o $TARGET_DIR/UUGear.o -c -Wall -Werror -fPIC UUGear.c
gcc  -shared -o $TARGET_DIR/libUUGear.so $TARGET_DIR/UUGear.o -lrt
gcc  -o $TARGET_DIR/SocketBroker SocketBroker.c UUGear.c -lrt

gcc  -o $TARGET_DIR/lsuu lsuu.c serial.c

echo "Copying shared object file to /usr/lib/..."

sudo cp $TARGET_DIR/libUUGear.so /usr/lib/

echo "Building project source files..."

PROJECT_DIR=$CURRENT_DIR/src

cd $PROJECT_DIR

g++ -L$TARGET_DIR -Wall readData.cpp -o $TARGET_DIR/readDatacpp -lUUGear -lrt -lm -std=c++11

cd $CURRENT_DIR

echo "Build End"
