#include <DNSServer.h>
#include <WiFi.h>
#include <AsyncTCP.h>
#include "ESPAsyncWebServer.h"
#include "SPIFFS.h"

#include "modifierPageWeb.h"
#include "serveurWeb.h"
#include "Reseaux/station.h"
#include "Reseaux/pointAcces.h"
#include "Fichiers/fichierSPIFFS.h"

DNSServer dnsServer;
AsyncWebServer server(80);

void setupServeurWeb()
{
    modifierFormPageConfigbd();
    modifierFormPageReseau();

    server.on("/main.css", HTTP_GET, [](AsyncWebServerRequest *request)
    {
        request->send(SPIFFS, "/main.css", "text/css");
        Serial.println("Page envoyée");
    });

    server.on("/", HTTP_GET, [](AsyncWebServerRequest *request)
    {
        Serial.println("Requete recue sur /");
        request->send(SPIFFS, "/index.html","text/html");
        Serial.println("Page envoyée");
    });

    server.on("/config-base-de-donnees", HTTP_GET, [](AsyncWebServerRequest *request)
    {
        Serial.println("Requete recue sur /config-base-de-donnees");
        Serial.println("Page modifiée");
        request->send(SPIFFS, "/configbd.html","text/html");
        Serial.println("Page envoyée");
    });

    server.on("/config-base-de-donnees", HTTP_POST, [] (AsyncWebServerRequest *request) 
    {
        Serial.println("Requete recue sur /config-base-de-donnees");
        String nom_sa = ""; 
        String localisation = "";
        String nom_bd = "";
        String nom_utilisateur = "";
        String mot_de_passe = "";
        for(int i=0;i<request->params() ;i++){
            AsyncWebParameter* p = request->getParam(i);
            if(p->name() == "nom_sa"){
                nom_sa = p->value();
            }
            else if(p->name() == "localisation"){
                localisation = p->value();
            } 
            else if(p->name() == "nom_bd"){
                nom_bd = p->value();
            } 
            else if(p->name() == "nom_utilisateur"){
                nom_utilisateur = p->value();
            } 
            else if(p->name() == "mot_de_passe"){
                mot_de_passe = p->value();
            } 
        }
        Serial.println("nom_sa: " + nom_sa);
        Serial.println("localisation: " + localisation);
        Serial.println("nom_bd: " + nom_bd);
        Serial.println("nom_utilisateur: " + nom_utilisateur);
        Serial.println("mot_de_passe: " + mot_de_passe);

        ecrireFichier("/infobd.txt",
            "nom_sa:"+nom_sa+
            "\nlocalisation:"+localisation+
            "\nnom_bd:"+nom_bd+
            "\nnom_utilisateur:"+nom_utilisateur+
            "\nmot_de_passe:"+mot_de_passe);

        request->redirect("http://"+WiFi.softAPIP().toString()+"/");  
    });

    server.on("/config-reseau", HTTP_GET, [](AsyncWebServerRequest *request)
    {
        Serial.println("Requete recue sur /config-reseau");
        modifierListeReseauxPageReseau();
        Serial.println("Page modifiée");
        request->send(SPIFFS, "/reseau.html","text/html");
        Serial.println("Page envoyée");
    });

    server.on("/config-reseau", HTTP_POST, [] (AsyncWebServerRequest *request) 
    {
        Serial.println("Requette recue sur /config-reseau");

        String nom_reseau = "";
        String type_eap = "";
        String nom_utilisateur = "";
        String identifiant = "";
        String mot_de_passe = "";
        for(int i=0;i<request->params() ;i++){
            AsyncWebParameter* p = request->getParam(i);
            if(p->name() == "nom_reseau"){
                nom_reseau = p->value();
            } 
            else if(p->name() == "type_eap"){
                type_eap = p->value();
            } 
            else if(p->name() == "nom_utilisateur"){
                nom_utilisateur = p->value();
            } 
            else if(p->name() == "identifiant"){
                identifiant = p->value();
            }
            else if(p->name() == "mot_de_passe"){
                mot_de_passe = p->value();
            }
        }
        Serial.println("nom_reseau: " + nom_reseau);
        Serial.println("type_eap: " + type_eap);
        Serial.println("nom_utilisateur: " + nom_utilisateur);
        Serial.println("identifiant: " + identifiant);
        Serial.println("mot_de_passe: " + mot_de_passe);

        ecrireFichier("/inforeseau.txt",
            "nom_reseau:"+nom_reseau+
            "\ntype_eap:"+type_eap+
            "\nnom_utilisateur:"+nom_utilisateur+
            "\nidentifiant:"+identifiant+
            "\nmot_de_passe:"+mot_de_passe);


        request->redirect("http://"+WiFi.softAPIP().toString()+"/");  
    });

    server.on("/config-acces-point", HTTP_POST, [] (AsyncWebServerRequest *request) 
    {
        Serial.println("Requette recue sur /config-acces-point");

        String ssid = "";
        String mot_de_passe = "";
        String mot_de_passe_confirm = "";
        for(int i=0;i<request->params() ;i++){
            AsyncWebParameter* p = request->getParam(i);
            if(p->name() == "ssid"){
                ssid = p->value();
            }
            else if(p->name() == "mot_de_passe"){
                mot_de_passe = p->value();
            } 
            else if(p->name() == "mot_de_passe_confirm"){
                mot_de_passe_confirm = p->value();
            } 
        }
        Serial.println("SSID: " + ssid);
        Serial.println("mot_de_passe: " + mot_de_passe);
        Serial.println("mot_de_passe_confirm: " + mot_de_passe_confirm);

        if(mot_de_passe == mot_de_passe_confirm)
        {
            ecrireFichier("/infoap.txt",
                "nom_ap:"+ssid+
                "\nmot_de_passe:"+mot_de_passe);
            request->redirect("http://"+WiFi.softAPIP().toString()+"/"); 
            String nomAP = recupererValeur("/infoap.txt","nom_ap");
            String motDePasseAP = recupererValeur("/infoap.txt","mot_de_passe");
            initReseauStationEtPointAcces();
            delay(100);
            creerPointAcces(nomAP,motDePasseAP);
        }
        else
        {
            request->redirect("http://"+WiFi.softAPIP().toString()+"/config-reseau");
        }
        
    });

    server.onNotFound ( [](AsyncWebServerRequest *request)
    {
        request->redirect("http://"+WiFi.softAPIP().toString()+"/");
    });
}

void setupServeurDNS()
{  
    dnsServer.start(53, "smart-campus.fr", WiFi.softAPIP());
}

void loopServeurDNS()
{
    dnsServer.processNextRequest();
}

void taskServeurDNS(void * parameter){
    while(true){
        loopServeurDNS();
        vTaskDelay(100);
    }
}

void activerServeurDNS()
{
    server.begin();

    delay(100);

    xTaskCreate(
        taskServeurDNS,
        "loopServeurWeb",
        10000,
        NULL,
        5,
        NULL
    );
}