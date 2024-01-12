//
// Created by Adrien on 20/12/23.
//

#ifndef SOFTWARE_HEURELOCAL_H
#define SOFTWARE_HEURELOCAL_H

// Inclusion des librairies principales
#include <WString.h>

/**
 * Initialise l'heure grace au serveur ntp 0.europe.pool.ntp.org
 * /!\ Necessite une connexion a internet /!\
*/
void initHeure();  

/**
 * Permet de recupérer la date actuelle
 * @return la date actuelle au format "YYYY-MM-DD hh:mm:ss" || "Date Error" si la date n'est pas accessible
*/
String getDate(); 

/**
 * Permet de recupérer l'année en cours
 * @return l'année en cours en tant qu'entier|| -1 si la date n'est pas accessible
*/
short getAnnee();

/**
 * Permet de récupérer le numéro mois en cours avec janvier == 1 et décembre == 12
 * @return le mois en cours en tant qu'entier || -1 si la date n'est pas accessible
*/
short getMois();

/**
 * Permet de recupérer le mois en cours écris en français
 * @return le mois en cours en français || "Date Error" si la date n'est pas accessible
 * E = {
 * "Janvier",
 * "Fevrier",
 * "Mars",
 * "Avril",
 * "Mai",
 * "Juin",
 * "Juillet",
 * "Aout",
 * "Septembre",
 * "Octobre",
 * "Novembre",
 * "Decembre"
 * }
 */
String getMoisString();

/**
 * Permet de recupérer le jour en cours
 * @return le jour en cours en entier || -1 si la date n'est pas accessible
*/
short getJour(); 

/**
 * Permet de recupérer le jour en cours écris en français
 * @return le jour en cours en français || "Date Error" si la date n'est pas accessible
 * E = {
 * "Lundi",
 * "Mardi",
 * "Mercredi",
 * "Jeudi",
 * "Vendredi",
 * "Samedi",
 * "Dimanche"
 * }
*/
String getJourSemaine();

/**
 * Permet de recupérer l'heure en cours en entier
 * @return l'heure en cours || -1 si la date n'est pas accessible
*/
short getHeure(); 

/**
 * Permet de recupérer la minute en cours en entier
 * @return La minute en cours || -1 si la date n'est pas accessible
*/
short getMinute();

/**
 * Permet de recupérer la seconde en cours en entier
 * @return La seconde en cours || -1 si la date n'est pas accessible
*/
short getSeconde(); 

#endif //SOFTWARE_HEURELOCAL_H
