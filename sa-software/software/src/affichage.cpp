#include "affichage.h"
#include "typedef.h"

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

void taskAffichage(void *pvParameters) {
    while(true){
        switch (page) {
            case TEMPERATURE :
                if (!isnan(temperature)) {
                    char temp[20];
                    sprintf(temp, "Temp : %.2f°C", temperature);
                    //display->drawString(0, 0, getDate());
                    display->drawString(0, 20, temp);
                }
                else {
                    //display->drawString(0, 0, getDate());
                    display->drawString(0, 20, "Erreur lors de la récupération de la température");
                }
                break;
            case HUMIDITE :
                if (!isnan(humidite)) {
                    char temp[17];
                    sprintf(temp, "Humidite : %.2f%s", humidite, "\"%\"");
                    //display->drawString(0, 0, getDate());
                    display->drawString(0, 20, temp);
                }
                else {
                    //display->drawString(0, 0, getDate());
                    display->drawString(0, 20, "Hum : Erreur");
                }
                break;
            case CO2 :
                if (co2 != -1) {
                    char temp[17];
                    sprintf(temp, "CO2 : %d ppm", co2);
                    //display->drawString(0, 0, getDate());
                    display->drawString(0, 20, temp);
                }
                else {
                    //display->drawString(0, 0, getDate());
                    display->drawString(0, 20, "CO2 : Erreur");
                }
            break;
        }
        display->display();
        delay(300000);
    }
}

void IRAM_ATTR changerPageDroite() {
    switch (page) {
        case TEMPERATURE :
            page = HUMIDITE;
            break;
        case HUMIDITE :
            page = CO2;
            break;
        case CO2 :
            page = TEMPERATURE;
            break;
    }
}

void IRAM_ATTR changerPageGauche() {
    switch (page) {
        case TEMPERATURE :
            page = CO2;
        break;
        case HUMIDITE :
            page = TEMPERATURE;
        break;
        case CO2 :
            page = HUMIDITE;
        break;
    }
}
