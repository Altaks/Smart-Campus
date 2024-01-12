//
// Created by Adrien on 20/12/23.
//

#ifndef POINTACCES_H
#define POINTACCES_H

// Inclusion des librairies principales
#include <WString.h>

/**
 * Initialise les paramètres wifi en mode point d'accès et station
 */
void initReseauStationEtPointAcces();

/**
 * Creer un point d'accès wifi
 * @param nom_ap nom du point d'accès
 * @param mot_de_passe mot de passe du point d'accès. Il doit faire au moins 8 caractères et au plus 63 caractères
 */
void creerPointAcces(String nom_ap, String mot_de_passe);
#endif

