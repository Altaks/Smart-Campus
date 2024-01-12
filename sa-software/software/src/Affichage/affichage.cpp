#include "affichage.h"
#include "typeDef.h"
#include "Heure/heureLocal.h"

SSD1306Wire * display;

bool initAffichage(struct Donnees* donnees)
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

bool initTacheAffichage(struct Donnees* donnees)
{
    xTaskHandle affichageTaskHandle;
    xTaskCreate( //création de la tâche
      taskAffichage,
      "taskTempEtHum",
      10000,
      (void*)donnees,
      1,
        &affichageTaskHandle
    );

    return affichageTaskHandle != NULL;
}

void afficher(struct Donnees * donnees){

    // reset de l'écran
    display->clear();

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

    // vériication de l'accès aux données
    if (donnees == nullptr) {
        display->drawString(0, 25, "Erreur de donnees");
        display->display();
        Serial.println("Erreur lors de la récupération des données pour l'affichage");
        return;
    }

    // affichage des données
    switch (donnees->page) {
        case TEMPERATURE :
            // vérification de l'accès des données
            if (donnees->tempEtHum == nullptr) {
                display->drawString(0, 25, "Temp : Erreur");
                Serial.println("Erreur lors de la récupération des données de température et d'humidité pour l'affichage");
                donnees->page = HUMIDITE;
                break;
            }
            // vérification de la présence des données
            if (donnees->tempEtHum->temperature != -1) {
                char temp[20];
                sprintf(temp, "Temp : %.2f°C", donnees->tempEtHum->temperature);
                display->drawString(0, 25, temp);
            }
            else {
                display->drawString(0, 25, "Temp : N/A");
            }
            donnees->page = HUMIDITE;
            break;
        case HUMIDITE :
            // vérification de l'accès des données
            if (donnees->tempEtHum == nullptr) {
                display->drawString(0, 25, "Hum : Erreur");
                Serial.println("Erreur lors de la récupération des données de température et d'humidité pour l'affichage");
                donnees->page = CO2;
                break;
            }
            // vérification de la présence des données
            if (donnees->tempEtHum->humidite != -1) {
                char temp[17];
                sprintf(temp, "Hum : %.2f%s", donnees->tempEtHum->humidite, "%");
                display->drawString(0, 25, temp);
            }
            else {
                display->drawString(0, 25, "Hum : N/A");
            }
            donnees->page = CO2;
            break;
        case CO2 :
            // vérification de l'accès des données
            if (donnees->co2 == nullptr) {
                display->drawString(0, 25, "CO2 : Erreur");
                donnees->page = TEMPERATURE;
                Serial.println("Erreur lors de la récupération des données de CO2 pour l'affichage");
                break;
            }
            // vérification de la présence des données
            if (*donnees->co2 != -1) {
                char temp[17];
                sprintf(temp, "CO2 : %d ppm", *donnees->co2);
                display->drawString(0, 25, temp);
            }
            else {
                display->drawString(0, 25, "CO2 : N/A");
            }
            donnees->page = TEMPERATURE;
        break;
    }
    // affichage des données de la salle
    display->display();
}

void taskAffichage(void *pvParameters) {
    struct Donnees * donnees = (struct Donnees *) pvParameters;
    while(true){
        delay(3 * 1000);
        afficher(donnees);
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
