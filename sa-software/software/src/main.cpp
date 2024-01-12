#include <Arduino.h>
#include <WiFi.h>
#include "Capteurs/tempEtHum.h"
#include "Heure/heureLocal.h"
#include "Capteurs/qualAir.h"
#include "Capteurs/presence.h"
#include "Affichage/affichage.h"
#include "Reseaux/pointAcces.h"
#include "Reseaux/station.h"
#include "typeDef.h"
#include "envois/envois.h"
#include "Serveur/serveurWeb.h"
#include "Fichiers/fichierSPIFFS.h"

TempEtHum* tempEtHum;
unsigned short* co2;
PAGE page;
Donnees* donnees;
char * nomSa;
char * salle;
char * nomUtilistaeurBD = "m2eq3";
char * pwd = "howjoc-dyjhId-hiwre0";
char * nomBD = "sae34bdm2eq3";
bool * presence;
unsigned short * lum;

void setup() {
    tempEtHum = new TempEtHum();
    co2 = new (unsigned short);
    page = TEMPERATURE;
    salle = "C005";
    nomSa = "ESP-018";
    lum = new (unsigned short);
    presence = new (bool);

    *lum = 0;

    donnees = new Donnees();
    donnees->tempEtHum = tempEtHum;
    donnees->co2 = co2;
    donnees->page;
    donnees->nomSa = nomSa;
    donnees->salle = salle;
    donnees->lum = lum;
    donnees->nomUtilisateurBD = nomUtilistaeurBD;
    donnees->pwd = pwd;
    donnees->nomBD = nomBD;
    donnees->presence = presence;


    Serial.begin(9600);
    while(!Serial);

    delay(1000);

    //initilaisation système de fichier
    initSystemeFichier();
    delay(100);
    
    String nomAP = recupererValeur("/infoap.txt","nom_ap");
    String motDePasseAP = recupererValeur("/infoap.txt","mot_de_passe");
    delay(100);

    //initialisation reseau
    initReseauStationEtPointAcces();
    delay(100);
    creerPointAcces(nomAP,motDePasseAP);
    delay(100);

    //Initialise le serveur web et le serveur DNS
    setupServeurWeb();
    setupServeurDNS();

    delay(100);

    activerServeurDNS();

    delay(100);
    bool affiche = initAffichage(donnees); //Initialise l'affichage
    displayText("Veuillez connecter\nle systeme a un\nréseau");

    delay(100);

    String nomReseau;

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
                //initialise l'heure s'il arrive a se connecter
                initHeure();
            }
            else
            {
                Serial.println("Echec de la connexion a "+nomReseau);
            }
        }
    }
    while(!estConnecte(nomReseau));

    activerEnregistrerListeReseau();

    delay(1000);

    //Initialise la tâche température et humidité 
    initTempEtHum(tempEtHum);

    delay(1000);

    //Initialise la tâche de CO2
    initQualAir(co2);

    delay(1000);
    initPresence(presence);

    delay(1000);
    initTacheAffichage(donnees);

    delay(1000);
    bool envoie = initEnvois(donnees); //Initialise l'envoi des données
    
}

void loop() 
{    
    afficherFichiers();

    afficherContenuFichier("/index.html");

    delay(10000);

    afficherContenuFichier("/reseau.html");

    delay(10000);

    afficherContenuFichier("/configbd.html");

    delay(10000);

    afficherContenuFichier("/listereseaux.txt");

    delay(10000);

    afficherContenuFichier("/inforeseau.txt");

    delay(10000);

    afficherContenuFichier("/infoap.txt");

    delay(10000);

    afficherContenuFichier("/infobd.txt");

    delay(10000);
}