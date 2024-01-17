#include "led.h"
#include "Capteurs/presence.h"
#include "Capteurs/tempEtHum.h"
#include "Capteurs/qualAir.h"

// On the ESP32S2 SAOLA GPIO is the NeoPixel.
#define PIN  18 

Adafruit_NeoPixel * led;

void initLED() {    
    //Single NeoPixel
    led = new Adafruit_NeoPixel(1, PIN, NEO_GRB + NEO_KHZ800);
    led->begin(); // INITIALIZE NeoPixel (REQUIRED)
    led->setBrightness(10);
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
        if(getTemperature() == -1 or getHumidite() == -1 or getCO2() == -1) {
            led->setPixelColor(0, led->Color(255,0,0));
            led->show();
            delay(250); 
            led->setPixelColor(0, led->Color(0,0,0));
        }
        else {
            led->setPixelColor(0, led->Color(0,255,0));
        }
        led->show();
        delay(250);
        
    }
}