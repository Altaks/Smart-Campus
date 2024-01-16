#ifndef LED_H
#define LED_H
#include <Adafruit_NeoPixel.h>

void initLED();

bool initTaskLED();

void taskLED(void *PvParameters);


#endif //LED_H