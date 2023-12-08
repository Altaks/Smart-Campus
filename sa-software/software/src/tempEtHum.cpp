#include "tempEtHum.h"


DHTesp CapteurTempEtHum;
TaskHandle_t tempEtHumTaskHandle = NULL;


void taskTempEtHum(void *pvParameters) {

  TempEtHum* values = (struct TempEtHum*) pvParameters; 
  for(;;){
    delay(2000); //délai avant nouvelle mesure
    
    TempAndHumidity TempEtHum = CapteurTempEtHum.getTempAndHumidity(); //récupère les valeurs d'humidité et de température
    values->temperature = (isnan(TempEtHum.temperature) ?  -1 : round(TempEtHum.temperature * 10.0)/10.0); //arrondi la valeur et reste a NaN si NaN pour ne pas "arrondir" NaN
    values->humidite = (isnan(TempEtHum.humidity) ?  -1 : round(TempEtHum.humidity * 10.0)/10.0);
    //Serial.printf("temp: %.1f; hum: %.1f;\n", values->temperature, values->humidite); //affiche dans le moniteur série les valeurs
  }
}

void initTempEtHum(struct TempEtHum* tempEtHum)
{
    CapteurTempEtHum.setup(pinTempEtHum, DHTesp::AM2302); //configuration du capteur avec pin et type
    xTaskCreate( //création de la tâche
      taskTempEtHum,
      "taskTempEtHum",
      10000,
      (void*)tempEtHum,
      10,
      NULL
    );
}


