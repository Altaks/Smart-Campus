#ifndef TYPEDEF_H
#define TYPEDEF_H
#include <WString.h>

// valeurs des pins
const int pinTempEtHum = 17;

// temperature et humidité :
struct TempEtHum{
    float temperature;
    float humidite;
};

// page courante dans les valeurs (1 -> Température, 2 -> Humidité, 3 -> CO2); // affichage de la luminosité et de la présence nom demandés par le client
enum PAGE {
    TEMPERATURE,
    HUMIDITE,
    CO2
};

struct Donnees{
    TempEtHum* tempEtHum;
    unsigned short * co2;
    unsigned short * lum;
    bool * presence;
    char * nomSa;
    char * salle;
    char * nomUtilisateurBD;
    char * pwd;
    char * nomBD;
    PAGE page;
};


#endif
