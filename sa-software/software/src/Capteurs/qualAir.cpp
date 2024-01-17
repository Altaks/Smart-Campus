#include "qualAir.h"

#include "Arduino.h"
#include <EEPROM.h>

#include <Wire.h>
#include "Adafruit_SGP30.h"

Adafruit_SGP30 sgp;

void initQualAir() {
  while (!Serial) { delay(10); } // Wait for serial console to open!

  Serial.println("SGP30 test");

  if (! sgp.begin()){
    Serial.println("Sensor not found :(");
    while (1);
  }
  Serial.print("Found SGP30 serial #");
}


int getCO2() {
  if (! sgp.IAQmeasure()) {
    Serial.println("Measurement failed");
    return -1;
  } else
        return sgp.eCO2;
}