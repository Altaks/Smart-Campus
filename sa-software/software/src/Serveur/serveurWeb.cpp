#include <DNSServer.h>
#include <WiFi.h>
#include <AsyncTCP.h>
#include "ESPAsyncWebServer.h"
#include "SPIFFS.h"

#include "captiveRequestHandler.h"
#include "modifierPageWeb.h"
#include "serveurWeb.h"
#include "Reseaux/station.h"
#include "Reseaux/pointAcces.h"

DNSServer dnsServer;
AsyncWebServer server(80);

String user_name;
String proficiency;

bool name_received = false;
bool proficiency_received = false;

void setupServeurWeb(){

    server.on("/", HTTP_GET, [](AsyncWebServerRequest *request){
        Serial.println("Requette recue");
        delay(100);
        request->send(SPIFFS, "/index.html","text/html");
    });

    server.on("/main.css", HTTP_GET, [](AsyncWebServerRequest *request)
    {
        request->send(SPIFFS, "/main.css", "text/css");
    });

    server.on("/config-reseau", HTTP_GET, [](AsyncWebServerRequest *request){
        Serial.println("Requette recue");
        ajouterListeReseauxWifi("<!--ListeReseaux-->", "<!--FinListeReseaux-->");
        delay(100);
        request->send(SPIFFS, "/reseau.html","text/html");
    });

    server.on("/config-base-de-donnees", HTTP_GET, [](AsyncWebServerRequest *request){
        Serial.println("Requette recue");
        delay(100);
        request->send(SPIFFS, "/configbd.html","text/html");
    });

    server.on("/config-reseau", HTTP_POST, [] (AsyncWebServerRequest *request) 
    {
        Serial.println("Requette recue");

        String SSID = "";
        String password = "";
        String login = "";
        String eap = "";
        for(int i=0;i<request->params() ;i++){
            AsyncWebParameter* p = request->getParam(i);
            if(p->name() == "SSID"){
                SSID = p->value();
            } 
            else if(p->name() == "password"){
                password = p->value();
            } 
            else if(p->name() == "username"){
                login = p->value();
            } 
            else if(p->name() == "eap"){
                eap = p->value();
            }
        }
        Serial.println("SSID: " + SSID);
        Serial.println("login: " + login);
        Serial.println("password: " + password);
        Serial.println("eap: " + eap);

        request->send(SPIFFS, "/index.html","text/html");
 
    });

    server.onNotFound ( [](AsyncWebServerRequest *request){
        Serial.println("Requette recue");
        ajouterListeReseauxWifi("<!--ListeReseaux-->", "<!--FinListeReseaux-->");
        delay(100);
        request->send(SPIFFS, "/index.html","text/html");
    });
}

void ajouterCaptiveRequest()
{
    server.addHandler(new CaptiveRequestHandler()).setFilter(ON_AP_FILTER);
}

void setupServeurDNS()
{  
    dnsServer.start(53, "*", WiFi.softAPIP());
}

void loopServeurDNS()
{
    dnsServer.processNextRequest();
}

void taskServeurDNS(void * parameter){
    while(true){
        loopServeurDNS();
        vTaskDelay(2000);
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
        1,
        NULL
    );
}