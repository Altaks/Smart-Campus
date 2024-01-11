//
// Created by Adrien on 20/12/23.
//

#include <WiFi.h>
#include <Arduino.h>

#include "station.h"
#include "typeDef.h"
#include "Fichiers/fichierSPIFFS.h"
#include "Heure/heureLocal.h"

void initReseauStation()
{
    // Pour connecter le wifi a un routeur
    WiFi.mode(WIFI_STA);
    WiFi.disconnect();
}

void taskEnregistrerReseaux(void * parameter){
    while(true){
        enregistrerListeReseaux();
        if (!estConnecte() && !recupererValeur("/inforeseau.txt","nom_reseau").isEmpty() && estDansFichier("/listereseaux.txt",recupererValeur("/inforeseau.txt","nom_reseau")))
        {
            String nomReseau      = recupererValeur("/inforeseau.txt","nom_reseau");
            int type_eap          = recupererValeur("/inforeseau.txt","type_eap").toInt();
            String password       = recupererValeur("/inforeseau.txt","nom_utilisateur");
            String identifiant    = recupererValeur("/inforeseau.txt","identifiant");
            String nomUtilisateur = recupererValeur("/inforeseau.txt","mot_de_passe");
            if(connexionWifi(nomReseau, wpa2_auth_method_t(type_eap), password ,identifiant, nomUtilisateur))
            {
                Serial.println("Connexion a "+nomReseau+" Reussie");
                initHeure();
            }
            else
            {
                Serial.println("Echec de la connexion a "+nomReseau);
            }
        }
        vTaskDelay(10 * 1000);
    }
}

void activerEnregistrementPeriodiqueReseaux()
{
    xTaskCreate(
        taskEnregistrerReseaux,
        "enregistrerListeReseaux",
        10000,
        NULL,
        5,
        NULL
    );
}

void enregistrerListeReseaux()
{
    // scan les reseaux alentours 
    int n = WiFi.scanNetworks();
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
    Serial.println();
}

void getListeReseau(String * listeReseauxDisponibles)
{
    // scan les reseaux alentours 
    int n = WiFi.scanNetworks();
    String listeReseauxDisponiblesStr = "";
    if(n == 0)
    {
        // si pas de réseaux (peut provenir d une surchage sur le port I2C (j ai pas tout compris))
        Serial.println("no network found"); 
    }
    else
    {
        // affiche la liste des réseaux trouvés
        Serial.println("networks found : ");
        for (int i = 0 ; i < n ; i++)
        {
            listeReseauxDisponiblesStr += WiFi.SSID(i) + "\n";
            Serial.print(String(i+1)+": "+WiFi.SSID(i)+"("+WiFi.RSSI(i)+")");
            Serial.println((WiFi.encryptionType(i) == WIFI_AUTH_OPEN)?" ":"*");
            delay(10);
        }
    }
    *listeReseauxDisponibles = listeReseauxDisponiblesStr;
    Serial.println();
}

bool estConnecte()
{
    Serial.println("Vérification connexion...");
    if(WiFi.status() == WL_CONNECTED)
    {
        return true;
    }
    else
    {
        return false;
    }
}

bool connexionWifi(const String& ssid, wpa2_auth_method_t methodeAutentification, const String& password, const String& identifiant, const String& nomUtilisateur)
{
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
