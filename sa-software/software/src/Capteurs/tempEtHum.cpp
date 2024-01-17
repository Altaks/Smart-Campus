#include "tempEtHum.h"


DHTesp CapteurTempEtHum;

void initTempEtHum()
{
    CapteurTempEtHum.setup(pinTempEtHum, DHTesp::AM2302); // Configuration du capteur avec pin et type
}

double getTemperature()
{
  TempAndHumidity TempEtHum = CapteurTempEtHum.getTempAndHumidity();
  return (isnan(TempEtHum.temperature) ?  -1 : round(TempEtHum.temperature * 10.0)/10.0);
}

double getHumidite()
{
  TempAndHumidity TempEtHum = CapteurTempEtHum.getTempAndHumidity();
  return (isnan(TempEtHum.humidity) ?  -1 : round(TempEtHum.humidity * 10.0)/10.0);
}