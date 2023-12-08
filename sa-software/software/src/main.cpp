#include <Arduino.h>
#include "tempEtHum.h"
#include "heure.h"
#include "qualAir.h"
#include "affichage.h"
#include "typeDef.h"


  TempEtHum* tempEtHum;
  unsigned short* co2;

void setup() {

  tempEtHum = new TempEtHum();
  co2 = new (unsigned short);

  Serial.begin(9600);
  while(!Serial);

  delay(1000);

  //Initialise la tâche température et humidité 
  initTempEtHum(tempEtHum);
  
  
  //Initialise la tâche l'horodatage
  initHeure();

  delay(1000);
  
  //Initialise la tâche de CO2
  initQualAir(co2);
}

void loop() {
  delay(2000);
  Serial.printf("temp: %.1f; hum: %.1f; co2: %d\n",tempEtHum->temperature,tempEtHum->humidite,*co2);
}