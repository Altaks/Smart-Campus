#include <Arduino.h>
#include "tempEtHum.h"
#include "heure.h"
#include "qualAir.h"
#include "affichage.h"
#include "typedef.h"


void setup() {

  temperature = NAN;
  humidite = NAN;
  co2 = -1;
  page = TEMPERATURE;

  Serial.begin(9600);
  while(!Serial);
  delay(100);

  //Initialise la tâche température et humidité 
  initTempEtHum();

  delay(100);

  //Initialise la ttaĉhe d'affichage sur l'écran
  initAffichage();

  delay(100);
  
  //Initialise la tâche l'horodatage
  initHeure();

  delay(100);
  
  //Initialise la tâche de CO2
  initQualAir();
}

void loop() {
  
}