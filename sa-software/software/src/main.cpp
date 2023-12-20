#include <Arduino.h>
#include "Capteurs/tempEtHum.h"
#include "Heure/heure.h"
#include "Capteurs/qualAir.h"
#include "Affichage/affichage.h"
#include "typeDef.h"


  TempEtHum* tempEtHum;
  unsigned short* co2;
  Donnees* donnees;

void setup() {

  tempEtHum = new TempEtHum();
  co2 = new (unsigned short);
  donnees = new Donnees();
  donnees->tempEtHum = tempEtHum;
  donnees->co2 = co2;


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

  delay(1000);
  initAffichage(donnees);
}

void loop() {
  delay(2000);
  Serial.printf("temp: %.1f; hum: %.1f; co2: %d\n",tempEtHum->temperature,tempEtHum->humidite,*co2);
}