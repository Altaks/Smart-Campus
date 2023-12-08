//
// Created by altaks on 06/12/23.
//

#ifndef SOFTWARE_QUALAIR_H
#define SOFTWARE_QUALAIR_H

// Inclusion des librairies principales
#include <Arduino.h>
#include <EEPROM.h>

// Inclusion des librairies du module
#include "sensirion_common.h"
#include "sgp30.h"

// Inclusion des librairies du projet
#include "typedef.h"

// Définition des constantes
#define LOOP_TIME_INTERVAL_MS  1000     // Temps d'attente entre chaque mesure de la qualité de l'air
#define BASELINE_IS_STORED_FLAG  (0x55) // Flag pour vérifier si la baseline est stockée dans l'EEPROM

/**
 * Fonction permettant d'initialiser le capteur de qualité de l'air
 */
void initQualAir(unsigned short * co2);

/**
 * Fonction permettant de récupérer la qualité de l'air et l'injecter dans la variable globale co2
 */
void taskQualAir(void * pvParameters);
#endif //SOFTWARE_QUALAIR_H
