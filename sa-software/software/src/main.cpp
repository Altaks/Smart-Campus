#include <Arduino.h>

#include "typeDef.h"

#include "Affichage/affichage.h"

#include "Capteurs/tempEtHum.h"
#include "Capteurs/qualAir.h"
#include "Capteurs/presence.h"

#include "envois/envois.h"

#include "Fichiers/fichierSPIFFS.h"

#include "Heure/heureLocal.h"

#include "Reseaux/pointAcces.h"
#include "Reseaux/station.h"

#include "Serveur/serveurWeb.h"
#include "Serveur/modifierPageWeb.h"

#include "LED/led.h"

void setup() {

    Serial.begin(9600);
    while(!Serial);

    delay(1000);
    Serial.println("1");
    // Initilaisation système de fichier
    initSystemeFichier();

    Serial.println("2");
    //LED
    initLED();
    Serial.println("2.5");
    //bool truc = initTaskLED();
    
    delay(100);
    Serial.println("3");
    // Récupère les informations du point d'accès 
    String nomAP = recupererValeur("/infoap.txt","nom_ap");
    String motDePasseAP = recupererValeur("/infoap.txt","mot_de_passe");
    Serial.println("4");
    // Initialisation reseau en mode STATION et POINT D'ACCES
    initReseauStationEtPointAcces();
    creerPointAcces(nomAP,motDePasseAP);
    Serial.println("5");
    //Initialise le serveur web et le serveur DNS
    setupServeurWeb();
    setupServeurDNS();
    activerServeurDNS();

    delay(100);

    //Initialise l'affichage
    bool affiche = initAffichage(); 
    
    //Affichage du nom de l'AP et de l'adresse IP a utilisé
    displayText("Nom du Wifi : \n" + nomAP+"\nIP : "+getIP(),0,10);

    String nomReseau;

    Serial.println("Connexion au reseau wifi");

    // tant que le SA n'est pas connecté à internet
    do
    {
        // enregistre la liste des reseaux dans le fichier listereseaux.txt
        enregistrerListeReseaux();

        nomReseau = recupererValeur("/inforeseau.txt","nom_reseau");

        // si le nom du reseau auquel se connecter est configuré
        // et que le reseau auquel se connecter est capté par le SA (enregistrer dans le fichier listereseaux.txt)
        if (!nomReseau.isEmpty() 
            && estDansFichier("/listereseaux.txt",nomReseau))
        {
            // recupère les valeurs dans le fichier inforeseau.txt
            int type_eap          = recupererValeur("/inforeseau.txt","type_eap").toInt();
            String password       = recupererValeur("/inforeseau.txt","mot_de_passe");
            String identifiant    = recupererValeur("/inforeseau.txt","identifiant");
            String nomUtilisateur = recupererValeur("/inforeseau.txt","nom_utilisateur");
            
            // Essaye de se connecter au reseau
            if(connexionWifi(nomReseau, wpa2_auth_method_t(type_eap), password ,identifiant, nomUtilisateur))
            {
                Serial.println("Connexion a "+nomReseau+" Reussie");
            }
            else
            {
                Serial.println("Echec de la connexion a "+nomReseau);
            }
        }
        delay(1000);
    }
    while(!estConnecte(nomReseau));
    
    // Initialise l'heure (peut prendre quelques secondes avant de se connecté au serveur ntp)
    initHeure();
    Serial.print("Initilisation de la date en cours");
    displayText("Initilisation de la\ndate en cours...");

    while (! dateEstInitilaisee())
    {
        Serial.print(".");
        delay(250);
    }
    Serial.println();
    
    // Désactive le point d'accès wifi (le serveur reste disponible en se connectant au même routeur)
    initReseauStation();

    // Affiche le contenu des fichiers contenant les informations a conservé dans le SA
    afficherContenuFichier("/infoap.txt");
    afficherContenuFichier("/infobd.txt");
    afficherContenuFichier("/inforeseau.txt");
    afficherContenuFichier("/listereseau.txt");

    // Active l'enregistrement périodique des reseaux wifi détectés par l'esp dans le fichier /listereseaux.txt
    // activerEnregistrerListeReseau();

    // Initialise les capteurs 
    initTempEtHum();
    initQualAir();
    initPresence();


    // Active l'affichage carrousel  
    if (affiche) {initTacheAffichage();}

    // Initialise l'envoi des données
    bool envoie = initEnvois(); 
}

void loop() 
{    
    delay(60 * 1000);
}