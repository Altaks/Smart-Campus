#ifndef TEMPETHUM_H
#define TEMPETHUM_H

#include "DHTesp.h"



/**
 * @param pvParameters
 * paramètres de la tâche
 * fonction qui gère la tâche
*/
void taskTempEtHum(void *pvParameters);

/**
 * @return bool
 * initialise le capteur et la tâche, et teste si l'initialisation s'est bien déroulée
*/
bool initTempEtHum();

#endif