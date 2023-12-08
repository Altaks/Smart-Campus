#ifndef AFFICHAGE_H
#define AFFICHAGE_H

#include <SSD1306Wire.h>
#include "typeDef.h"

bool initAffichage(struct Donnees*);

void taskAffichage(void *pvParameters);

#endif
