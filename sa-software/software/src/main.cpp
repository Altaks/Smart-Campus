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

const String* nomReseau    = new String("eduroam");
const char* password       = "txg;3afks64@KmMy";
const char* identifiant    = "ksimon";
const char* nomUtilisateur = "ksimon";

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
    initReseauStation();

    delay(1000);

    //Initialise la tâche température et humidité 
    initTempEtHum(tempEtHum);

    delay(1000);

    //Initialise la tâche de CO2
    initQualAir(co2);

    delay(1000);
    initPresence(presence);

    delay(1000);
    bool affiche = initAffichage(donnees); //Initialise l'affichage

    delay(1000);
    bool envoie = initEnvois(donnees); //Initialise l'envoi des données
}

void loop() {

    if(estConnecte())
    {
        Serial.println("L'ESP est connecté !");
        Serial.println(getDate());
    }
    else
    {
        String *listeReseauxDisponibles = new String("");
        getListeReseau(listeReseauxDisponibles);
        String reseaux = *listeReseauxDisponibles;
        delete listeReseauxDisponibles;
        Serial.println("Liste reseaux recupere");
        

        // Split les réseaux dans un tableau
        int nbReseaux = 0;
        for(int i = 0 ; i < reseaux.length() ; i++)
        {
            if(reseaux[i] == '\n') nbReseaux++;
        }
        String listeReseaux[nbReseaux];
        String reseau = "";
        int numReseau = 0;
        for(int i = 0 ; i < reseaux.length() ; i++)
        {
            if(reseaux[i] == '\n')
            {
                listeReseaux[numReseau] = reseau;
                reseau = "";
                numReseau++;
            }
            else
            {
                reseau+= reseaux[i];
            }
        }
        Serial.println("Liste reseaux découpée");

        //vérifie si le reseaux auquel on souhaite se connecter est disponnible
        int cnt = 0;
        while(*nomReseau != listeReseaux[cnt] && cnt < nbReseaux) cnt++;
        Serial.println("Recherche de "+*nomReseau+" dans la liste des reseaux");

        
        if(cnt < nbReseaux) //si le reseau est trouvé 
        {
            if(connexionWifi(*nomReseau, WPA2_AUTH_TTLS, password ,identifiant, nomUtilisateur))
            {
                Serial.println("Connexion a "+*nomReseau+" Reussie");
                initHeure();
            }
            else
            {
                Serial.println("Echec de la onnexion a "+*nomReseau);

            }
        }
        else
        {
            Serial.println("Echec de la onnexion a "+*nomReseau);
        }
    }
    delay(1000);
}