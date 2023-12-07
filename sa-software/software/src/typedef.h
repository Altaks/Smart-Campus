#ifndef TYPEDEF_H
#define TYPEDEF_H
#include <WString.h>

// valeurs des pins
const int pinTempEtHum = 17;

// temperature est humidité :
static float temperature;
static float humidite;

// qualite de l'air :
static int co2;


// page courante dans les valeurs (1 -> Température, 2 -> Humidité, 3 -> CO2);
enum PAGE {
    TEMPERATURE,
    HUMIDITE,
    CO2
};

static PAGE page;


#endif
