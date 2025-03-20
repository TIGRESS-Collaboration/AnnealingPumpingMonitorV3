#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <mqueue.h>
#include <unistd.h>
#include <time.h>
#include <math.h>

extern "C"{
	#include "srcUUGear/UUGear.h"
}

//Define constants

#define EXTERNAL 1		// For external ADC reference voltage
#define INTERNAL 0		// For Vcc to be the ADC reference voltage
#define ADC_RES 1023	// ADC resolution
#define GAIN_IG 3.47
#define GAIN_TC2 3.33
#define SAMPLE_NUM 50
#define MID_NUM 20
#define PAST_NUM 10
#define PIN_NUM 4

#define IG_ALGORITHM 11 // For the Ion-Gauge pressure conversion
#define TC_ALGORITHM 4  // For the thermocouple pressure conversion

#ifndef TEMP_CONV_1
	#define TEMP_CONV_1 146.8331143
#endif

#ifndef TEMP_OFFSET_1
	#define TEMP_OFFSET_1 -256.9097503
#endif

#ifndef TEMP_CONV_2
    #define TEMP_CONV_2 132.7957508
#endif

#ifndef TEMP_OFFSET_2
    #define TEMP_OFFSET_2 -231.2247224
#endif

#ifndef PRESS_OFFSET_1
	#define PRESS_OFFSET_1 0.0
#endif

#ifndef PRESS_OFFSET_2
	#define PRESS_OFFSET_2  0
#endif

#ifndef PT100_IN1 		// Define the input pins
	#define PT100_IN1 2
#endif

#ifndef PT100_IN2
	#define PT100_IN2 3
#endif

#ifndef IG_IN
	#define IG_IN 5
#endif

#ifndef TC2_IN
	#define TC2_IN 4
#endif

#ifndef CAL_PT100 		// These are FINAL READING calibrations (ie. in Volts, degree Celsius, Torr, etc...)
	#define CAL_PT100 0.0
#endif

#ifndef CAL_IG
    #define CAL_IG 0.00 // This is likely to change later
#endif

#ifndef CAL_TC2
    #define CAL_TC2 -0.00 // This is likely to change later
#endif

#ifndef CAL_5V
	#define CAL_5V 3.325
#endif

// And any other constants...
const char* UUGEAR_ADDR = "UUGear-Arduino-9046-1419";


// Just some conversion functions for temperature and pressure
// double cal is the calibration offset between the measurement from the detector lab boxes to the calculated value here
float volt2Pressure(double voltage, int expFactor, float gain, double cal) {
	double intpart, decpart;
	int exp;
	float pressure;


	voltage = (voltage + cal) * gain;
	decpart = modf(voltage, &intpart);
	exp = (int)intpart - expFactor;

	// Decode the mantissa
	if (exp < 0) 
		pressure = ((decpart + 0.1) / 0.11)/pow(10,-1*exp);
	else
		pressure = ((decpart + 0.1) / 0.11)*pow(10,exp);
	 
	return pressure;
}

float volt2Temperature(float voltage, float conv, float offset, double cal) {
    double temp;
	temp = (double) ((voltage+cal) * conv + offset); // Simple linear relation
    return temp;
}

// Simple moving average filter 
float filterSMA(int newData[PIN_NUM], int oldData[PAST_NUM * PIN_NUM], float average[PIN_NUM]) {
	int ii, jj;
	int sum[PIN_NUM];

	// First shift all old values back in old array
	for (ii = 0; ii < PIN_NUM; ii++) {
		sum[ii] = 0;
		for (jj = ii * PAST_NUM; jj < PAST_NUM * (ii + 1); jj++) {
			if (jj == PAST_NUM * (ii + 1) - 1) {
				oldData[jj] = newData[ii];
			} else {
				oldData[jj] = oldData[jj + 1];
			}
			sum[ii] += oldData[jj]; // Then add the new value to the current sum.
		}			
		average[ii] = (float) sum[ii] / PAST_NUM; // Take the average
	}
}

// This is the overall noise filter. It has 3 steps:
// 1) Take a sample set of data. Order them
// 2) Take the mean of the median values in the ordered set
// 3) Filter this value further by taking the simple moving average with previous measured values
float readADCNoiseFilter(UUGearDevice dev, int pin[PIN_NUM], float readings[PIN_NUM], int oldData[PIN_NUM * PAST_NUM]) {
	int sample[PIN_NUM * SAMPLE_NUM];
	int sum[PIN_NUM];
	float sumSMA[PIN_NUM];
	int swap = 0;
	int ii, jj;

	for (ii = 0; ii < PIN_NUM; ii++) {
		for (jj = ii * SAMPLE_NUM; jj < SAMPLE_NUM * (ii + 1); jj++) {
			sample[jj] = analogRead(&dev, pin[ii]);
			//usleep(500);
			//printf("Reading: %d from pin %d\n", sample[jj], pin[ii]);
		}
	}

    for (ii = 0; ii < PIN_NUM; ii++) {
        for (jj = ii * SAMPLE_NUM; jj < SAMPLE_NUM * (ii + 1); jj++) {
			if (sample[jj] > sample [jj + 1]) {
				swap = sample[jj];
				sample[jj] = sample[jj + 1];
	            sample[jj + 1] = swap;
			}
        }
    }

    for (ii = 0; ii < PIN_NUM; ii++) {
        sum[ii] = 0;
    }

    for (ii = 0; ii < PIN_NUM; ii++) {
        for (jj = 0; jj < MID_NUM; jj++) {
            sum[ii] += sample[ii*SAMPLE_NUM+(int)((SAMPLE_NUM-MID_NUM)/2)+jj];
        }
    }

	filterSMA(sum, oldData, sumSMA);

	for (ii = 0; ii < PIN_NUM; ii++) {
		readings[ii] = sumSMA[ii] / MID_NUM;
	}
}

int main(int argc, char **argv)
{
	// Just some little Arduino setup tiddlybits
    setupUUGear();
    setShowLogs(1);
    UUGearDevice dev = attachUUGearDevice (strdup(UUGEAR_ADDR));

	analogReference(&dev, EXTERNAL); // set AREF to external (Arduino's 3.3V regulated line, in this case)

    if (dev.fd != -1) {
		int oldADC[PIN_NUM * PAST_NUM] = {0}; // Storing old raw ADC conversion data
		int sample[SAMPLE_NUM] = {0};
		/*
		for (j = 0; j < PIN_NUM*PAST_NUM; j++) {
			oldADC[j] = 0;
		}
		*/

		char strCurrTime[21];
        float voltPT1001, voltPT1002, voltSTIG, voltSTTC2, tempPT1001, tempPT1002, presSTIG, presSTTC2;
		int pin[] = {PT100_IN1, PT100_IN2, IG_IN, TC2_IN};  // I needed an array of the pin numbers to cycle...this is messy (bleh) but it works!
		float readings[PIN_NUM];  // An array of the final readings

		voltPT1001 = 0; voltPT1002 = 0; voltSTIG = 0; voltSTTC2 = 0;
		tempPT1001 = 0; tempPT1002 = 0; presSTIG = 0; presSTTC2 = 0;

		time_t pastTime;
		time(&pastTime);
		struct tm tmPTime;
		tmPTime  = *localtime(&pastTime);

		time_t currTime;
		struct tm tmCurrTime;

		// Initializing files
		FILE *currRead; // This should overwrite old file
		char currReadFN[] = "../../../site/www/datalogs/currentReading.txt";
	    printf("Creating file \"%s\"\n", currReadFN);
   	 	currRead = fopen(currReadFN, "w"); // Overwrite old file
    	if (currRead == NULL) {
        	perror("Failed to create and open file");
        	return EXIT_FAILURE;
    	}
    	fclose(currRead);


		FILE *datalog_timed; // This should simply create a new file
        char datalogTimedFN[56];
        strftime(datalogTimedFN, sizeof(datalogTimedFN), "../../../site/www/datalogs/datalog_%Y%m%d_%H%M%S.csv", &tmPTime);
        printf("Creating file \"%s\"\n", datalogTimedFN);
        datalog_timed = fopen(datalogTimedFN, "a"); // Append to old file (if it exists...)
        if (datalog_timed == NULL) {
            perror("Failed to create and open file");
            return EXIT_FAILURE;
        }
		fprintf(datalog_timed,"Date,RTD1,RTD1V,RTD2,RTD2V,IG,IGV,TC2,TC2V\n");
        fclose(datalog_timed);

		FILE *datalog; // This should simply create a new file
		char datalogFN[] = "../datalogs/datalog.csv";
		printf("Opening file \"%s\"\n", datalogFN);
		datalog = fopen(datalogFN, "a"); // Append to old file (if it exists...)
		if (datalog == NULL) {
            perror("Failed to create and open file");
            return EXIT_FAILURE;
        }
		fprintf(datalog, "Date,RTD1,RTD2,IG,TC2\n");
        fclose(datalog);


        while (1) {
			//readADCNoiseFilter(dev, pin, readings, oldADC); // Read in them values, and apply prelim noise filtering

			for (int i = 0; i < PIN_NUM; i++) {
				int sampleSum = 0;
				for (int j = 0; j < SAMPLE_NUM; j++) {
					sampleSum += analogRead(&dev, pin[i]);
				}
				readings[i] = sampleSum;
			}

			// Convert ADC to voltage
            voltPT1001 = (float)(readings[0]/SAMPLE_NUM * CAL_5V) / ADC_RES;
			voltPT1002 = (float)(readings[1]/SAMPLE_NUM * CAL_5V) / ADC_RES;
            voltSTIG = (float) (readings[2]/SAMPLE_NUM * CAL_5V) / ADC_RES;
            voltSTTC2 = (float) (readings[3]/SAMPLE_NUM * CAL_5V) / ADC_RES;

			// Convert voltages to actual pressure/temperature readouts
			presSTIG = volt2Pressure(voltSTIG, IG_ALGORITHM, GAIN_IG, CAL_IG);
			presSTTC2 = volt2Pressure(voltSTTC2, TC_ALGORITHM, GAIN_TC2, CAL_TC2);
			tempPT1001 = volt2Temperature(voltPT1001, TEMP_CONV_1, TEMP_OFFSET_1, CAL_PT100);
            tempPT1002 = volt2Temperature(voltPT1002, TEMP_CONV_2, TEMP_OFFSET_2, CAL_PT100);

			time(&currTime); // Get the time
			tmCurrTime  = *localtime(&currTime);

			// Check if new datalog file should be created (rollover is at midnight each day)
			if(tmCurrTime.tm_mday > tmPTime.tm_mday) {
				strftime(datalogTimedFN, sizeof(datalogTimedFN), "../datalogs/datalog_%Y%m%d_%H%M%S.csv", &tmCurrTime);
        		printf("Creating file \"%s\"\n", datalogTimedFN);
        		datalog_timed = fopen(datalogTimedFN, "a"); // Append to old file (if it exists...)
        		if (datalog_timed == NULL) {
            		perror("Failed to create and open file");
            		return EXIT_FAILURE;
        		}
				fprintf(datalog_timed, "Date,RTD1,RTD1V,RTD2,RTD2V,IG,IGV,TC2,TC2V\n");
        		fclose(datalog_timed);
			}

			tmPTime = tmCurrTime;

			strftime(strCurrTime, sizeof(strCurrTime), "%Y/%m/%d %H:%M:%S", &tmCurrTime);
			printf("Time: %s    PT100(1): %.1f C    PT100(2): %.1f C    IG: %.2E Torr    TC2: %.2E Torr\n", strCurrTime, tempPT1001, tempPT1002, presSTIG, presSTTC2);

			currRead = fopen(currReadFN, "w");
			fprintf(currRead, "<b>Time:</b> %s <br><b>PT100 (1):</b> %.1f\u2103 &nbsp; %.3fV <br><b>PT100 (2):</b> %.1f\u2103 &nbsp; %.3fV <br><b>IG:</b> %.2E Torr &nbsp; %.3fV<br><b>TC2:</b> %.2E Torr&nbsp; %.3fV", strCurrTime, tempPT1001, voltPT1001, tempPT1002, voltPT1002,  presSTIG, (voltSTIG+CAL_IG)*GAIN_IG,  presSTTC2, (voltSTTC2+CAL_TC2)*GAIN_TC2);
			fflush(currRead);
			fsync(fileno(currRead));
			fclose(currRead);
			
			datalog = fopen(datalogFN, "a");
			fprintf(datalog, "%s,%.1f,%.1f,%.2E,%.2E\n", strCurrTime, tempPT1001, tempPT1002, presSTIG, presSTTC2);
			fflush(datalog);
            fsync(fileno(datalog));
			fclose(datalog);

            datalog_timed = fopen(datalogTimedFN, "a");
			fprintf(datalog_timed, "%s,%.1f,%.3f,%.1f,%.3f,%.2E,%.3f,%.2E,%.3f\n", strCurrTime, tempPT1001, voltPT1001, tempPT1002, voltPT1002, presSTIG, voltSTIG, presSTTC2, voltSTTC2);
			fflush(datalog_timed);
            fsync(fileno(datalog_timed));
            fclose(datalog_timed);

            usleep(1000000); // Time delay between full measurements
        }
        detachUUGearDevice(&dev);
    }
    else {
        printf("Can not open UUGear device.\n");
    }

    cleanupUUGear();

    return 0;
}











