//
// Created by Adrien on 20/12/23.
//

#ifndef POINTACCES_H
#define POINTACCES_H

// Inclusion des librairies principales
#include <WString.h>

/**
 * Initialise les paramètres wifi en mode point d'accès
 */
void initReseauPointAcces();

/**
 * Initialise les paramètres wifi en mode point d'accès et station
 */
void initReseauStationEtPointAcces();

/**
 * Affiche le nom du point d'accès
 */
void afficherNomPointAcces();

/**
 * Recupere le nom du point d'accès
 * @return le nom du point d'accès en String
*/
String getNomPointAcces();

/**
 * Affiche l'adresse IP du point d'accès
 */
void afficherAdresseIPPoinAcces();


/**
 * recuppere l'adresse IP du point d'accès
 * @return l'adresse IP du point d'accès en String
 */
String getAdresseIPPoinAcces();


/**
 * Creer un point d'accès wifi
 * @param aSsid nom du point d'accès
 * @param aPassword mot de passe du point d'accès. Il doit faire au moins 8 caractères et au plus 63 caractères
 */
void creerPointAcces(String aSsid, String aPassword);
#endif

