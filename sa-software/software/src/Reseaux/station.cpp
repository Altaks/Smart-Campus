//
// Created by Adrien on 20/12/23.
//

#include <WiFi.h>
#include <Arduino.h>

#include "station.h"
#include "typeDef.h"
#include "Fichiers/fichierSPIFFS.h"
#include "Heure/heureLocal.h"

void taskEnregistrerListeReseau(void * parameter){
    while(true){
        vTaskDelay(10 * 1000);
        enregistrerListeReseaux();
    }
}

void activerEnregistrerListeReseau()
{
    // Active la tache taskConnexionReseau
    xTaskCreate(
        taskEnregistrerListeReseau,
        "Connexion au reseau wifi",
        10000,
        NULL,
        11,
        NULL
    );
}

void enregistrerListeReseaux()
{
    // scan les reseaux alentours 
    int n = WiFi.scanNetworks();
    
    /*
     * Enregistre la liste des reseaux dans la varible listeReseauxDisponiblesStr au format :
     *  nb_reseaux:2
     *  1:reseau1
     *  2:reseau2
    */
    String listeReseauxDisponiblesStr = "nb_reseaux:"+String(n)+"\n";
    if(n > 0)
    {
        for (int i = 0 ; i < n ; i++)
        {
            listeReseauxDisponiblesStr += String(i+1)+ ":" + WiFi.SSID(i) + "\n";
            delay(10);
        }
    }

    ecrireFichier("/listereseaux.txt", listeReseauxDisponiblesStr);
}

bool estConnecte(String nomReseau)
{

    return (WiFi.SSID() == nomReseau && WiFi.status() == WL_CONNECTED);
}

bool connexionWifi(const String& ssid, wpa2_auth_method_t methodeAutentification, const String& password, const String& identifiant, const String& nomUtilisateur)
{
    // Réalise la connexxion du SA a un reseau en wifi avec le mode de connexion choisi
    switch(methodeAutentification)
    {
        case WPA2_AUTH_PEAP:
            WiFi.begin(ssid, WPA2_AUTH_PEAP, identifiant, nomUtilisateur, password);
            break;
        case WPA2_AUTH_TTLS:
            WiFi.begin(ssid, WPA2_AUTH_TTLS, identifiant, nomUtilisateur, password);
            break;
        case WPA2_AUTH_TLS:
            WiFi.begin(ssid, password);
            break;
    }

    Serial.println("Connexion au réseau "+ssid);
    
    // Verifie si le sa se connecte pendant 60 secondes
    // retourne true si il arrive a se connecté; false sinon 
    for(int counter = 0 ; counter <= 60 && WiFi.status() != WL_CONNECTED ; counter++)
    {
        delay(500);
        Serial.println("Connexion en cours...");
        if(counter >= 60)
        {

            return false;
        }
    }
    Serial.println("Connexion réussie !");
    return true;
}
