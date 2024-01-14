#include <SPIFFS.h>
#include "Reseaux/station.h"
#include "Fichiers/fichierSPIFFS.h"

#include "modifierPageWeb.h"

void modifierFormPageConfigbd()
{
    String ip = getIP() ;
    modifierFichier("/configbd.html", "<!--DebutFormHead-->", "<!--FinFormHead-->", "<form action=\"http://" + ip + "/config-base-de-donnees\" method=\"POST\">");
}

void modifierFormPageReseau()
{
    String ip = getIP() ;
    modifierFichier("/reseau.html", "<!--DebutFormHeadReseau-->", "<!--FinFormHeadReseau-->", "<form action=\"http://" + ip + "/config-reseau\" method=\"POST\">");
    modifierFichier("/reseau.html", "<!--DebutFormHeadAp-->", "<!--FinFormHeadAp-->", "<form action=\"http://" + ip + "/config-acces-point\" method=\"POST\">");
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