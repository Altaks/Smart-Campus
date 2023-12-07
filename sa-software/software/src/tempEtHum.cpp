#include "tempEtHum.h"
#include "typedef.h"
#include <cmath>

DHTesp CapteurTempEtHum;
TaskHandle_t tempEtHumTaskHandle = NULL;


void taskTempEtHum(void *pvParameters) {
  while(true){
    TempAndHumidity TempEtHum = CapteurTempEtHum.getTempAndHumidity(); //récupère les valeurs d'humidité et de température
    temperature = (isnan(TempEtHum.temperature) ?  -1 : round(TempEtHum.temperature * 10.0)/10.0); //arrondi la valeur et reste a NaN si NaN pour ne pas "arrondir" NaN
    humidite = (isnan(TempEtHum.humidity) ?  -1 : round(TempEtHum.humidity * 10.0)/10.0);
    
    Serial.printf("temp: %.1f; hum: %.1f;\n", temperature, humidite); //affiche dans le moniteur série les valeurs
    vTaskDelay(pdMS_TO_TICKS(2000)); //délai avant nouvelle mesure
  }
}

void initTempEtHum()
{
    CapteurTempEtHum.setup(pinTempEtHum, DHTesp::AM2302); //configuration du capteur avec pin et type
    xTaskCreate( //création de la tâche
      taskTempEtHum,
      "taskTempEtHum",
      1000,
      nullptr,
      1,
      &tempEtHumTaskHandle
    );
    if (tempEtHumTaskHandle == NULL) { //test si la tâche a été créée
      Serial.println("Failed to start task for temperature update");
      return;
    }
}


