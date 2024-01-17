#include "qualAir.h"

#include "Arduino.h"
#include <EEPROM.h>

#include <Wire.h>
#include <Adafruit_SGP30.h>

Adafruit_SGP30 sgp;

void initQualAir() {
  while (!Serial) { delay(10); } // Wait for serial console to open!

  Serial.println("Initialisation capteur CO2");

  if (! sgp.begin()){
    Serial.println("Capteur CO2 non trouvé");
    while (true);
  }
  Serial.println("Capteur CO2 connecté");
}


int getCO2() {
  if (! sgp.IAQmeasure()) {
    Serial.println("Mesure échouée");
    return -1;
  } else
        return sgp.eCO2;
}

int getCO2WithoutMeasure() {
  return sgp.eCO2;
}