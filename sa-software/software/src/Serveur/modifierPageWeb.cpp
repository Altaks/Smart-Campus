#include <SPIFFS.h>
#include "WiFi.h"
#include "Fichiers/fichierSPIFFS.h"

#include "modifierPageWeb.h"

void modifierFormPageConfigbd()
{
    modifierFichier("/configbd.html", "<!--DebutFormHead-->", "<!--FinFormHead-->", "<form action=\"http://" + WiFi.softAPIP().toString() + "/config-base-de-donnees\" method=\"POST\">");
}

void modifierFormPageReseau()
{
    modifierFichier("/reseau.html", "<!--DebutFormHeadReseau-->", "<!--FinFormHeadReseau-->", "<form action=\"http://" + WiFi.softAPIP().toString() + "/config-reseau\" method=\"POST\">");
    Serial.println("reseau.html head formReseau modifié");
    modifierFichier("/reseau.html", "<!--DebutFormHeadAp-->", "<!--FinFormHeadAp-->", "<form action=\"http://" + WiFi.softAPIP().toString() + "/config-acces-point\" method=\"POST\">");
    Serial.println("reseau.html head formAp modifié");
}

void modifierListeReseauxPageReseau()
{
    int n = recupererValeur("/listereseaux.txt","nb_reseaux").toInt();
    String contenu = "";
    if(n > 0)
    {
        String ssid;
        for(int i = 1 ; i <= n ; i++)
        {
            ssid = recupererValeur("/listereseaux.txt",String(i));
            contenu += "<option value=\"" + ssid + "\">" + ssid + "</option>";
        }
    }
    modifierFichier("/reseau.html", "<!--ListeReseaux-->", "<!--FinListeReseaux-->", contenu);
    Serial.println("reseau.html liste des reseaux diponibles modifiée");
}