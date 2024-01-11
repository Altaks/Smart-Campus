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
 * appelle la fonction enregistrerListeReseaux toute les 10 secondes
*/
void activerEnregistrementPeriodiqueReseaux();

/**
 *  Permet d'enregistrer la liste des reseaux dans le fichier listereseaux.txt 
 *  la première ligne correspond au nombre de reseaux
 *  les autres lignes contiennent les réseaux
 *  exemple du fichier listereseaux.txt :
 *  nb_reseaux:2
 *  1:reseau1
 *  2:reseau2
*/
void enregistrerListeReseaux();

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
