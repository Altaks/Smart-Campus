#include "affichage.h"
#include "typeDef.h"
#include "heure.h"

SSD1306Wire * display;
PAGE page = TEMPERATURE;

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
    xTaskCreate( //création de la tâche
      taskAffichage,
      "taskTempEtHum",
      10000,
      (void*)donnees,
      1,
      NULL
    );
    return true;
}

void taskAffichage(void *pvParameters) {
    struct Donnees * donnees = (struct Donnees *) pvParameters;
    while(true){
        delay(10 * 1000);
        display->clear();

        String dateTime = String(getJour()) + "/" + String(getMois()) + "/" + String(getAnnee()) + " " + String(getHeure()) + ":" + String(getMinute());

        display->drawString(0, 0, dateTime);
        switch (page) {
            case TEMPERATURE :
                if (donnees->tempEtHum->temperature != -1) {
                    char temp[20];
                    sprintf(temp, "Temp : %.2f°C", donnees->tempEtHum->temperature);
                    display->drawString(0, 25, temp);
                }
                else {
                    display->drawString(0, 25, "Temp : N/A");
                }
                page = HUMIDITE;
                break;
            case HUMIDITE :
                if (donnees->tempEtHum->humidite != -1) {
                    char temp[17];
                    sprintf(temp, "Hum : %.2f%s", donnees->tempEtHum->humidite, "%");
                    display->drawString(0, 25, temp);
                }
                else {
                    display->drawString(0, 25, "Hum : N/A");
                }
                page = CO2;
                break;
            case CO2 :
                if (*donnees->co2 != -1) {
                    char temp[17];
                    sprintf(temp, "CO2 : %d ppm", *donnees->co2);
                    display->drawString(0, 25, temp);
                }
                else {
                    display->drawString(0, 25, "CO2 : N/A");
                }
                page = TEMPERATURE;
            break;
        }
        display->display();
    }
}
