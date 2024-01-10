#ifndef AFFICHAGE_H
#define AFFICHAGE_H

#include <SSD1306Wire.h>
#include "typeDef.h"

/**
 * @brief Initialise l'affichage
 * Initialise l'affichage et la tâche d'affichage
 * @param donnees Pointeur vers les données
 * @return true si l'initialisation s'est bien passée
*/
bool initAffichage(struct Donnees*);


/**
 * @brief Tâche d'affichage
 * Tâche d'affichage qui affiche les données sur l'écran toutes les 3 secondes en carrousel
*/
void taskAffichage(void *pvParameters);

/**
 * @brief Affiche les données sur l'écran
 * Affiche les données sur l'écran
 * @param donnees Pointeur vers les données
*/
void afficher(struct Donnees * donnees);

#endif
