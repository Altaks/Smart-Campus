#include <Arduino.h>
#include "tempEtHum.h"
#include "heure.h"
#include "qualAir.h"
#include "affichage.h"
#include "typedef.h"


void setup() {


  TempEtHum tempEtHum;
  int co2;
  page = TEMPERATURE;

  Serial.begin(9600);
  while(!Serial);
  delay(1000);
  
  //Initialise la tâche de CO2
  initQualAir();
  
  delay(300);
  
  //Initialise la tâche l'horodatage
  initHeure();

  //wdelay(100);

  //Initialise la tâche température et humidité 
  initTempEtHum(&tempEtHum);

  delay(300);
  
}

void loop() {
  
}