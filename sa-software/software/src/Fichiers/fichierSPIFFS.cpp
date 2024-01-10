#include "SPIFFS.h"

#include "fichierSPIFFS.h"

void initSystemeFichier()
{
    SPIFFS.begin();
}

void afficherFichiers()
{
    File root = SPIFFS.open("/");
    File file = root.openNextFile();

    while(file)
    {
        Serial.print("File: ");
        Serial.println(file.name());
        file.close();
        file = root.openNextFile();
    }
}

void afficherContenuFichier(String nomFichier)
{
    File file = SPIFFS.open(nomFichier);

    if(!file)
    {
        Serial.println("Erreur lors de l'ouverture du fichier");
        return;
    }

    Serial.println("Contenu du fichier: ");
    while(file.available())
    {
        Serial.write(file.read());
    }
    Serial.println();
    file.close();
}

void modifierFichier(String nomFichier, String baliseDebut, String baliseFin, String contenu)
{
    File file = SPIFFS.open(nomFichier);
    
        if(!file)
        {
            Serial.println("Erreur lors de l'ouverture du fichier");
            return;
        }
    
        String ancienContenuFichier = "";
        while(file.available())
        {
            ancienContenuFichier += (char)file.read();
        }
        file.close();
    
        int indexDebut = ancienContenuFichier.indexOf(baliseDebut);
        int indexFin = ancienContenuFichier.indexOf(baliseFin);
    
        if(indexDebut == -1 || indexFin == -1)
        {
            Serial.println("Erreur lors de la modification du fichier");
            return;
        }
    
        String nouveauContenuFichier = ancienContenuFichier.substring(0, indexDebut + baliseDebut.length()) + contenu + ancienContenuFichier.substring(indexFin);

        file = SPIFFS.open(nomFichier, FILE_WRITE);
        if(!file)
        {
            Serial.println("Erreur lors de l'ouverture du fichier");
            return;
        }
    
        file.print(nouveauContenuFichier);
        file.close();
}
