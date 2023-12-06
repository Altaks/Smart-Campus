#include <Arduino.h>
#include "tempEtHum.h"
#include "heure.h"
#include "qualAir.h"
#include "affichage.h"
#include "typedef.h"

void taskTempEtHum(void *pvParameters) {
  while(true){
    loopTempEtHum();
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

  temperature = NAN;
  humidite = NAN;
  co2 = -1;
  page = TEMPERATURE;

  Serial.begin(9600);
  while(!Serial);

  // Température et humidité 
  /*initTempEtHum();
  xTaskCreate(
    taskTempEtHum,
    "taskTempEtHum",
    1000,
    NULL,
    1,
    NULL
  );*/

  // Affichage
  bool isDisplayInit = initAffichage();
  if (isDisplayInit) {
    xTaskCreate(
      taskAffichage,
      "taskAffichage",
      1000,
      NULL,
      1,
      NULL
    );
  }

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
  
}