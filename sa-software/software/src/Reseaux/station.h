//
// Created by Adrien on 20/12/23.
//

#ifndef SOFTWARE_STATION_H
#define SOFTWARE_STATION_H

// Inclusion des librairies principales
#include <WString.h>

/**
 * Permet d'initialiser l'accès au wifi dit mode "station"
 */
void initReseauStation();

/**
 * retourne la liste des reseaux disponibles séparer par le séparateur "\n"
*/
void getListeReseau(String * listeReseauxDisponibles);

/**
 * Verifie si l'ESP est connecté a un reseau
 * @return true si est connecté
*/
bool estConnecte();

/**
 * Connecte l'ESP a un reseau 
 * @return true si connexion reussie
 */
bool connexionWifi(const String& ssid, wpa2_auth_method_t methodeAutentification = WPA2_AUTH_TLS, const String& password = "", const String& identifiant = "", const String& nomUtilisateur = "");

#endif //SOFTWARE_STATION_H
