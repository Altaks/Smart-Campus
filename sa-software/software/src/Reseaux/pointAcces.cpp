#include <WiFi.h>
#include "pointAcces.h"

void initReseauStationEtPointAcces()
{
    // Pour pouvoir creer un point d'acces et connecter le SA a un wifi
    WiFi.mode(WIFI_AP_STA);
    WiFi.disconnect();
}

void creerPointAcces(String nom_ap, String mot_de_passe)
{
    // Cré le point d'acces avec les information données 
    WiFi.softAP(nom_ap,mot_de_passe);      
 
    Serial.print("[+] AP Created with IP Gateway ");
    Serial.println(WiFi.softAPIP());
}