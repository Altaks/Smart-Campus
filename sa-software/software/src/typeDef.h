#ifndef TYPEDEF_H
#define TYPEDEF_H

// temperature est humidité :
static float temperature;
static float humidite;

// qualite de l'air :
static int co2;
static String curDate;

// placer les variables globales ici :

// page courante dans les valeurs (1 -> Température, 2 -> Humidité, 3 -> CO2);

enum PAGE {
    TEMPERATURE,
    HUMIDITE,
    CO2
};

static PAGE page;


#endif
