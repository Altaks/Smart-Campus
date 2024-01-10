#ifndef ENVOIS_H
#define ENVOIS_H

#include <HTTPClient.h>
#include <WiFi.h>
#include "Reseaux/station.h"


#include "typeDef.h"
#include "Heure/heureLocal.h"


/**
 * Fonction permettant d'initialiser l'envois des données
 * Créer une tâche qui envoie les données toutes les 5 minutes
 * @param donnees pointeur vers la structure de données
 * @return true si l'initialisation s'est bien passée, false sinon
*/
bool initEnvois(struct Donnees*);

/**
 * Fonction permettant d'envoyer les données
 * Effectue les érificationde la connexion au réseau et de l'envoi des données
 * @param pvParameters paramètre de la tâche, non utilisés ici
*/
void taskEnvois(void *pvParameters);

/**
 * Fonction permettant d'envoyer les données
 * @param donnees pointeur vers la structure de données
 * @return 0 si l'envoi s'est bien passé, le code d'erreur http si positif ou le code d'erreur interne sinon
*/
int envoyer(struct Donnees*);

/**
 * Fonction permettant de convertir un code d'erreur en String
 * @param code le code d'erreur
*/
String errreurToString(int);

const String nomsValeurs[] = {"temp", "hum", "co2", "lum", "pres"};


#endif