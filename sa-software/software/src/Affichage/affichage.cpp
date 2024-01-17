#include "affichage.h"

#include "typeDef.h"
#include "Capteurs/qualAir.h"
#include "Capteurs/tempEtHum.h"
#include "Heure/heureLocal.h"
#include "Reseaux/station.h"

SSD1306Wire * display;

int carrouselDelay = 3000;
int flicker = 6;

bool initAffichage()
{
    Serial.println("______________________________________");
    Serial.println("Début de l'initialisation de l'écran :");
    Serial.println("______________________________________");

    Serial.println("Definition de la variable de lécran ...");
    display = new SSD1306Wire(0x3c, SDA, SCL);
    Serial.println("Variable de l'écran définie.");

    Serial.println("Initialisation de la variable ...");
    if (!display->init()) {
        Serial.println("Erreur lors de l'initialisation de la variable ...");
        return false;
    }

    Serial.println("Paramètrage de l'affichage ...");
    display->setFont(ArialMT_Plain_16);
    display->setTextAlignment(TEXT_ALIGN_LEFT);

    Serial.println("Variable initialisee.");
    Serial.println("Ecran Initialise.");
    Serial.println("______________________________________");

    return true;
}

bool initTacheAffichage()
{
    xTaskHandle affichageTaskHandle;
    xTaskCreate( //création de la tâche
      taskAffichage,
      "Affichage des données en local",
      3000,
      NULL,
      1,
        &affichageTaskHandle
    );

    return affichageTaskHandle != NULL;
}

void afficher(PAGE &page){


    // selectionne la police d'écriture
    display->setFont(ArialMT_Plain_16);

    // affichage de la date et de l'heure
    String dateTime;
    String ip = getIP();
  
    if (getDate() == "Date Error"){
        dateTime = "Erreur de date";
        Serial.println("Erreur lors de la récupération de la date pour l'affichage");
    }
    else {
        dateTime = String(getJour()) + "/" + String(getMois()) + "/" + String(getAnnee()) + " " + String(getHeure()) + ":" + String(getMinute());
    }

    for(int carrousel=0; carrousel<3; carrousel++)
    {
        float donnees;
        String donneesString;
        const char* format;
        
        // affichage des données
        switch (page) {
            case TEMPERATURE :
                donneesString = "Temp :";
                donnees = getTemperature();
                format = "%s %.2f°C";
                page = HUMIDITE;
                break;
            case HUMIDITE :
                donneesString = "Hum :";
                donnees = getHumidite();
                format = "%s %.2f%%";
                page = CO2;
                break;
            case CO2 :
                donneesString = "CO2 :";
                donnees = (float)getCO2();
                format = "%s %.0fppm";
                page = TEMPERATURE;
                break;
        }

        if (donnees != -1) {
            char temp[20];
            sprintf(temp, format, donneesString, donnees);
            displayResetInfos(dateTime, ip);
            display->drawString(0, 25, temp);
            display->display();
            delay(carrouselDelay);
        }
        else {
            char tempErr[20];
            sprintf(tempErr, "%s Err", donneesString);
            for(int flick=0; flick<flicker; flick++) {
                displayResetInfos(dateTime, ip);
                display->display();
                delay(carrouselDelay/flicker);
                displayResetInfos(dateTime, ip);
                display->drawString(0, 25, tempErr);
                display->display();
                delay(carrouselDelay/flicker);
            }
        }
    }
}

void taskAffichage(void *pvParameters) {

    PAGE page = TEMPERATURE;
    while(true){
        afficher(page);
    }
}

void displayText(String text, int x, int y, int fontSize, bool centered){
    display->clear();
    switch (fontSize)
    {
    case 10:
        display->setFont(ArialMT_Plain_10);
        break;
    case 24:
        display->setFont(ArialMT_Plain_24);
        break;
    default:
        display->setFont(ArialMT_Plain_16);
        break;
    }
    display->setTextAlignment(TEXT_ALIGN_LEFT);

    if (centered){        
        int w = text.length() * fontSize / 2;
        x = (display->getWidth() - w) / 2;
        y = (display->getHeight() - fontSize) / 2;
    }

    display->drawString(x, y, text);
    display->display();
}

void displayResetInfos(String dateTime, String ip) {
    // reset de l'écran
    display->clear();

    // affichage de la date et de l'heure
    display->drawString(0, 0, dateTime);

    // affichage de l'adresse IP
    display->drawString(0,48,"IP : " + ip);
}
