#include <Arduino.h>
#include "tempEtHum.h"
#include "heure.h"
#include "qualAir.h"
#include "affichage.h"

void taskTempEtHum(void *pvParameters) {
  while(true){
    loopTempEtHum();
    delay(300000);
  }
}


void taskAffichage(void *pvParameters) {
  while(true){
    loopAffichage();
    delay(300000);
  }
}

void taskQualAir(void *pvParameters) {
    while(true){
        loopQualAir();
        delay(1000);
    }
}

void setup() {
  Serial.begin(9600);
  while(!Serial);

  // Température et humidité 
  initTempEtHum();
  xTaskCreate(
    taskTempEtHum,
    "taskTempEtHum",
    1000,
    NULL,
    1,
    NULL
  );

  // Affichage
  initAffichage();
  xTaskCreate(
    taskAffichage,
    "taskAffichage",
    1000,
    NULL,
    1,
    NULL
  );

  // Heure
  initHeure();

  initQualAir();
  xTaskCreate(
    taskQualAir,
    "taskQualAir",
    10000,
    NULL,
    1,
    NULL
  );

}

void loop() {
  Serial.println(getDate());
  delay(5000);
}