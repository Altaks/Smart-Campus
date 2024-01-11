#include <SPIFFS.h>
#include "WiFi.h"
#include "Fichiers/fichierSPIFFS.h"

#include "modifierPageWeb.h"


void modifierPageAccueil(String contenu)
{
    modifierFichier("/index.html", "<!--DebutMessageValidation-->", "<!--FinMessageValidation-->", contenu);
}

void modifierPageConfigbd()
{
    modifierFichier("/configbd.html", "<!--DebutFormHead-->", "<!--FinFormHead-->", "<form action=\"http://" + WiFi.softAPIP().toString() + "/config-base-de-donnees\" method=\"POST\">");
}

void modifierPageReseau(String contenuMessageErreur)
{
    modifierFichier("/reseau.html", "<!--DebutFormHeadReseau-->", "<!--FinFormHeadReseau-->", "<form action=\"http://" + WiFi.softAPIP().toString() + "/config-reseau\" method=\"POST\">");
    Serial.println("reseau.html head formReseau modifié");
    // scan les resaux alentours 
    int n = recupererValeur("/listereseaux.txt","nb_reseaux").toInt();
    String contenu = "";
    Serial.println("recup les reseaux");
        
    if(n > 0)
    {
        String ssid;
        for(int i = 1 ; i <= n ; i++)
        {
            ssid = recupererValeur("/listereseaux.txt",String(i));
            contenu += "<option value=\"" + ssid + "\">" + ssid + "</option>";
        }
    }
    Serial.println("add reseau dans contenu");
    modifierFichier("/reseau.html", "<!--ListeReseaux-->", "<!--FinListeReseaux-->", contenu);
    Serial.println("reseau.html liste des reseaux diponibles modifiée");
    modifierFichier("/reseau.html", "<!--DebutFormHeadAp-->", "<!--FinFormHeadAp-->", "<form action=\"http://" + WiFi.softAPIP().toString() + "/config-acces-point\" method=\"POST\">");
    Serial.println("reseau.html head formAp modifié");
    modifierFichier("/reseau.html", "<!--DebutMessageErreur-->", "<!--FinMessageErreur-->", contenuMessageErreur);
    Serial.println("reseau.html message d'erreur modifié");
}