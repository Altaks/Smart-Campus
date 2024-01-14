#ifndef AFFICHAGE_H
#define AFFICHAGE_H

#include <SSD1306Wire.h>
#include "typeDef.h"

/**
 * @brief Initialise l'affichage
 * Initialise l'affichage 
 * @return true si l'initialisation s'est bien passée
*/
bool initAffichage();

/**
 * @brief Initialise la tâche d'affichage
 * Initialise la tâche d'affichage
 * @return true si l'initialisation s'est bien passée
*/
bool initTacheAffichage();

/**
 * @brief Tâche d'affichage
 * Tâche d'affichage qui affiche les données sur l'écran toutes les 3 secondes en carrousel
*/
void taskAffichage(void *pvParameters);

/**
 * @brief Affiche les données sur l'écran
 * Affiche les données sur l'écran
 * @param PAGE Page a afficher
*/
void afficher(PAGE &);

int displayText(String text, int x = 0, int y = 0, int fontSize = 16, bool centered = false);

#endif
