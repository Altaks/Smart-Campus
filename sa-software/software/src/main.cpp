#include <Arduino.h>
#include "tempEtHum.h"
#include "heure.h"
#include "qualAir.h"
#include "affichage.h"


void setup() {
  Serial.begin(9600);
  while(!Serial);

  // Température et humidité 
  initTempEtHum();
}

void loop() {

}