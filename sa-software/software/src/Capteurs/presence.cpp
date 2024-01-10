#include "presence.h"

/**
 * Correspond au pin physique de la détection de présence
*/
#define PIN_PRESENCE 4

/**
 * Pointeur vers la variable de présence
*/
bool * presence_ptr = nullptr;

/**
 * Fonction permettant de détecter la présence d'une personne dans la salle
 * @param pvParameters paramètre de la tâche, non utilisés ici
*/
void taskPresence(void *pvParameters){
    if(presence_ptr != nullptr) {
        *presence_ptr = (bool)(digitalRead(PIN_PRESENCE));
        Serial.println(*presence_ptr ? "Présence détectée" : "Pas de présence");
    }
    delay(3000);
}

void initPresence(bool * presence){
    Serial.println("Initialisation de la détection de présence");

    pinMode(PIN_PRESENCE, INPUT);
    presence_ptr = presence;

    xTaskCreate(
        taskPresence,
        "taskPresence",
        10000,
        NULL,
        1,
        NULL
    );
}