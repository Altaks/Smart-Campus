#ifndef LED_H
#define LED_H
#include <Adafruit_NeoPixel.h>

void initLED();

bool initTaskLED();

void taskLED(void *PvParameters);

void setEnvoieState(bool envoie);

void setLEDColor(int r, int g, int b);

#endif //LED_H