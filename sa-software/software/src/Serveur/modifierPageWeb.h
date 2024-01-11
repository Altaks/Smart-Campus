#ifndef MODIFIERPAGEWEB_H
#define MODIFIERPAGEWEB_H
#include <WString.h>

/**
 * Ajoute un message en rouge en haut au fichier /index.html
 * @param contenu Contenu du message à afficher
*/
void modifierPageAccueil(String contenu = "");

/**
 * Change les entêtes du formulaire du fichier /configbd.html
*/
void modifierPageConfigbd();

/**
 * Ajoute les reseau alentour au select et change les entêtes des formulaires du fichier /reseau.html
*/
void modifierPageReseau(String contenuMessageErreur = "");

#endif