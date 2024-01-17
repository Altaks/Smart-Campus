#include "led.h"
#include "Capteurs/presence.h"
#include "Capteurs/tempEtHum.h"
#include "Capteurs/qualAir.h"

// On the ESP32S2 SAOLA GPIO is the NeoPixel.
#define PIN  18 

Adafruit_NeoPixel * led;

const int RED[3] = {255,0,0};
const int ORANGE[3] = {255,60,0};
const int YELLOW[3] = {255,255,0};
const int GREEN[3] = {0,255,0};
const int BLUE[3] = {0,0,255};
const int PURPLE[3] = {255,0,255};
const int MAGENTA[3] = {187,38,73};
const int WHITE[3] = {255,255,255};
const int OFF[3] = {0,0,0};

bool envoieState = true;

void initLED() {    
    //Single NeoPixel
    led = new Adafruit_NeoPixel(1, PIN, NEO_GRB + NEO_KHZ800);
    led->begin(); // INITIALIZE NeoPixel (REQUIRED)
    led->setBrightness(10);
    led->setPixelColor(0, led->Color(ORANGE[0],ORANGE[1],ORANGE[2]));
    led->show();
}

bool initTaskLED() {
    xTaskHandle ledTaskHandle;
    xTaskCreate( //création de la tâche
      taskLED,
      "Gestion des LEDs",
      5000,
      NULL,
      1,
        &ledTaskHandle
    );

    return ledTaskHandle != NULL;
}

void taskLED(void *PvParameters) {
    while(true){
        if(getTemperature() == -1) {
            led->setPixelColor(0, led->Color(RED[0],RED[1],RED[2]));
            led->show();
            delay(250); 
        }

        if(getHumidite() == -1) {
            led->setPixelColor(0, led->Color(ORANGE[0],ORANGE[1],ORANGE[2]));
            led->show();
            delay(250); 
        }

        if(getCO2() == -1) {
            led->setPixelColor(0, led->Color(YELLOW[0],YELLOW[1],YELLOW[2]));
            led->show();
            delay(250); 
        }

        if(!envoieState) {
            led->setPixelColor(0, led->Color(MAGENTA[0],MAGENTA[1],MAGENTA[2]));
            led->show();
            delay(250);
        }
 
        led->setPixelColor(0, led->Color(OFF[0],OFF[1],OFF[2]));
        led->show();
        delay(250);
    }
}

void setEnvoieState(bool envoie)
{
    envoieState = envoie;
}
