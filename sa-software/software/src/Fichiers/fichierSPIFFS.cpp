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

    Serial.println("Contenu du fichier "+nomFichier+" : ");
    while(file.available())
    {
        Serial.write(file.read());
    }
    Serial.println("\n");
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

        Serial.println("Index balise "+baliseDebut+" : "+indexDebut);
        Serial.println("Index balise "+baliseFin+" : "+indexFin);
        
        if(indexDebut == -1 || indexFin == -1)
        {
            Serial.println("Erreur lors de la modification du fichier");
            return;
        }
    
        String nouveauContenuFichier = ancienContenuFichier.substring(0, indexDebut + baliseDebut.length()) + "\n" + contenu + "\n" + ancienContenuFichier.substring(indexFin);

        file = SPIFFS.open(nomFichier, FILE_WRITE);
        if(!file)
        {
            Serial.println("Erreur lors de l'ouverture du fichier");
            return;
        }
    
        file.print(nouveauContenuFichier);
        file.close();
}

String recupererValeur(String nomFichier, String nomValeur)
{
    File file = SPIFFS.open(nomFichier);
    nomValeur+=':';
    if(!file)
    {
        Serial.println("Erreur lors de l'ouverture du fichier");
        return "";
    }

    String valeur = "";
    String debutLigne = "";
    char lastChar = '0'; 
    while(file.available() && nomValeur != debutLigne)
    {
        lastChar = file.read();
        debutLigne += lastChar;
        if(lastChar == '\n')
        {
            debutLigne = "";
        }
    }

    while(file.available())
    {
        lastChar = file.read();
        if(lastChar =='\n') break;
        valeur += lastChar;
    }
    file.close();
    return valeur;
}

bool estDansFichier(String nomFichier, String texte)
{
    File file = SPIFFS.open(nomFichier);

    if(!file)
    {
        Serial.println("Erreur lors de l'ouverture du fichier");
        return false;
    }

    String contenuFichier = "";
    while(file.available())
    {
        contenuFichier += (char)file.read();
    }
    file.close();

    return contenuFichier.indexOf(texte) >= 0;    
}

void ecrireFichier(String nomFichier, String contenu)
{
    if(SPIFFS.exists(nomFichier)) SPIFFS.remove(nomFichier);
    File file = SPIFFS.open(nomFichier,FILE_WRITE,true);

    if(!file)
    {
        Serial.println("Erreur lors de l'ouverture du fichier");
        return ;
    }
    
    file.print(contenu);
    file.close();
}