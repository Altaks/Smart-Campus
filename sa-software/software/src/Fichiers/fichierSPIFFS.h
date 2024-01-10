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

#endif