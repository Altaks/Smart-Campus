#ifndef AFFICHAGE_H
#define AFFICHAGE_H

#include <Arduino.h>
#include <Wire.h>               // Only needed for Arduino 1.6.5 and earlier
#include "SSD1306Wire.h"        // legacy: #include "SSD1306.h"
// OR #include "SH1106Wire.h"   // legacy: #include "SH1106.h"
#include "heure.h"


bool initAffichage();
void taskAffichage(void *pvParameters);
void changerPageDroite();
void changerPageGauche();

#endif