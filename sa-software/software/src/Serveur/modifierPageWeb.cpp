#include <SPIFFS.h>
#include "WiFi.h"
#include "Fichiers/fichierSPIFFS.h"

#include "modifierPageWeb.h"

void ajouterListeReseauxWifi(String baliseDebut, String baliseFin)
{
    // scan les resaux alantours 
    int n = WiFi.scanNetworks();

    String contenu = "";
        
    if(n > 0)
    {
        for(int i = 0 ; i < n ; i++)
        {
            contenu += "<option value=\"" + WiFi.SSID(i) + "\">" + WiFi.SSID(i) + "</option>";
        }
    }
    modifierFichier("/reseau.html", baliseDebut, baliseFin, contenu);
    modifierFichier("/reseau.html", "<!--DebutFormHead-->", "<!--FinFormHead-->", "<form action=\"http://" + WiFi.softAPIP().toString() + "/connexion\" method=\"POST\">");
}