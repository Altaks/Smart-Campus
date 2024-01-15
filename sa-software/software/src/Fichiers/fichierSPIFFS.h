#ifndef FICHIERSPIFFS_H
#define FICHIERSPIFFS_H
#include <WString.h>

/**
 * Initialise le système de fichier 
*/
void initSystemeFichier();

/**
 * Affiche tout les fichier enregistrer dan le système de fichier
*/
void afficherFichiers();

/**
 * Affiche le contenu d'un fichier
 * @param nomFichier 
*/
void afficherContenuFichier(String nomFichier);

/**
 * Modifie le contenu d'un fichier
 * @param nomFichier 
 * @param baliseDebut 
 * @param baliseFin 
 * @param contenu 
*/
void modifierFichier(String nomFichier, String baliseDebut, String baliseFin, String contenu);

/**
 * Permet de recuppérer une valeur dans un fichier donné
 * la ligne doit etre au format "nomvaleur:valeur"
 * @param nomFichier nom du fichier où une valeur est cherchée
 * @param nomValeur nom de la valeur à recupérer 
 * @return la valeur associé | un chaine de charactère vide si la valeur n'est pas trouvé
*/
String recupererValeur(String nomFichier, String nomValeur);

/**
 * Vérifie si un texte est trouvable dans un fichier donné
 * @param nomFichier nom du fichier
 * @param texte texte a chercher
 * @return true si le texte est trouvable dans le fichier | false sinon
*/
bool estDansFichier(String nomFichier, String texte);

/**
 * Réécrit le contenu d'un fichier
 * Crée le fichier si nécéssaire
 * @param nomFichier nom du fichier où une valeur est cherchée
 * @param contenu nouveau contenu du fichier  
*/
void ecrireFichier(String nomFichier, String contenu);

#endif