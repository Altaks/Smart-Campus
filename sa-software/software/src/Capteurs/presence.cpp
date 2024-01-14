#include "presence.h"

/**
 * Correspond au pin physique de la détection de présence
*/
#define PIN_PRESENCE 4

void initPresence(){
    Serial.println("Initialisation de la détection de présence");

    pinMode(PIN_PRESENCE, INPUT);
}

bool getPresence()
{
    return digitalRead(PIN_PRESENCE);
}