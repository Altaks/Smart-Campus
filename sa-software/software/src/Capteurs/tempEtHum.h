#ifndef TEMPETHUM_H
#define TEMPETHUM_H

#include "DHTesp.h"
#include "typeDef.h"
#include <cmath>


/**
 * initialise le capteur et la tâche, et teste si l'initialisation s'est bien déroulée
*/
void  initTempEtHum();

/**
 * Fonction permettant de recupérer la valeur du capteur de température
 * @return La température mesurer par le capteur de température
*/
double getTemperature();

/**
 * Fonction permettant de recupérer la valeur du capteur d'humidité
 * @return L'humidité dans l'air en pourcentage mesurer par le capteur d'humidité
*/
double getHumidite();

#endif