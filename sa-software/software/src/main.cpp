#include <Arduino.h>
#include "tempEtHum.h"
#include "heure.h"
#include "qualAir.h"
#include "affichage.h"
#include "typedef.h"


void setup() {

  temperature = -1;
  humidite = -1;
  co2 = -1;
  page = TEMPERATURE;

  Serial.begin(9600);
  while(!Serial);
  delay(1000);
  
  //Initialise la tâche de CO2
  initQualAir();
  
  delay(300);
  /*
  //Initialise la tâche l'horodatage
  initHeure();

  delay(100);
*/
  //Initialise la tâche température et humidité 
  initTempEtHum();

  delay(300);
  
}

void loop() {
  
}