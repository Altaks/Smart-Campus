#include <WiFi.h>
#include "pointAcces.h"

void initReseauPointAcces()
{
    WiFi.mode(WIFI_AP);
    WiFi.disconnect();
}

void initReseauStationEtPointAcces()
{
    WiFi.mode(WIFI_AP_STA);
    WiFi.disconnect();
}

void afficherNomPointAcces()
{
    Serial.print("[+] AP Created with SSID");
    Serial.println(WiFi.softAPSSID());
}

String getNomPointAcces()
{
    return WiFi.softAPSSID();
}

void afficherAdresseIPPoinAcces()
{
    Serial.println(WiFi.softAPIP());
}

String getAdresseIPPoinAcces()
{
    return WiFi.softAPIP().toString();
}

void creerPointAcces(String aSsid, String aPassword)
{
    WiFi.softAP(aSsid,aPassword);      
 
    Serial.print("[+] AP Created with IP Gateway ");
    Serial.println(WiFi.softAPIP());
}