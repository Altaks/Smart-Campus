#include "affichage.h"

#include "typeDef.h"
#include "Capteurs/qualAir.h"
#include "Capteurs/tempEtHum.h"
#include "Heure/heureLocal.h"
#include "Reseaux/station.h"

SSD1306Wire * display;

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
      "taskTempEtHum",
      10000,
      NULL,
      1,
        &affichageTaskHandle
    );

    return affichageTaskHandle != NULL;
}

void afficher(PAGE &page){

    // reset de l'écran
    display->clear();

    // selectionne la police d'écriture
    display->setFont(ArialMT_Plain_16);

    // affichage de la date et de l'heure
    String dateTime;

    if (getDate() == "Date Error"){
        dateTime = "Erreur de date";
        Serial.println("Erreur lors de la récupération de la date pour l'affichage");
    }
    else {
        dateTime = String(getJour()) + "/" + String(getMois()) + "/" + String(getAnnee()) + " " + String(getHeure()) + ":" + String(getMinute());
    }

    display->drawString(0, 0, dateTime);

    display->drawString(0,48,"IP : " + getIP());

    // affichage des données
    switch (page) {
        case TEMPERATURE :
            // vérification de la présence des données
            if (getTemperature() != -1) {
                char temp[20];
                sprintf(temp, "Temp : %.2f°C", getTemperature());
                display->drawString(0, 25, temp);
            }
            else {
                display->drawString(0, 25, "Temp : N/A");
            }
            page = HUMIDITE;
            break;
        case HUMIDITE :
            // vérification de la présence des données
            if (getHumidite() != -1) {
                char temp[17];
                sprintf(temp, "Hum : %.2f%s", getHumidite(), "%");
                display->drawString(0, 25, temp);
            }
            else {
                display->drawString(0, 25, "Hum : N/A");
            }
            page = CO2;
            break;
        case CO2 :
            // vérification de la présence des données
            if (getCO2() != -1) {
                char temp[17];
                sprintf(temp, "CO2 : %d ppm", getCO2());
                display->drawString(0, 25, temp);
            }
            else {
                display->drawString(0, 25, "CO2 : N/A");
            }
            page = TEMPERATURE;
        break;
    }
    // affichage des données de la salle
    display->display();
}

void taskAffichage(void *pvParameters) {

    PAGE page = TEMPERATURE;
    while(true){
        delay(3 * 1000);
        afficher(page);
    }
}

int displayText(String text, int x, int y, int fontSize, bool centered){
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
        int x = (display->getWidth() - w) / 2;
        int y = (display->getHeight() - fontSize) / 2;
    }

    display->drawString(x, y, text);
    display->display();
    return 0;
}
