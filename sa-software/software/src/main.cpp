#include <Arduino.h>
#include "tempEtHum.h"
#include "heure.h"
#include "qualAir.h"
#include "affichage.h"


void setup() {
  Serial.begin(9600);
  while(!Serial);
  delay(100);

  //Initialise la tâche température et humidité 
  initTempEtHum();
}

void loop() {

}