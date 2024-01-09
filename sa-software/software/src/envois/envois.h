#ifndef ENVOIS_H
#define ENVOIS_H

#include <HTTPClient.h>
#include <WiFi.h>


#include "typeDef.h"
#include "Heure/heureLocal.h"

bool initEnvois(struct Donnees*);

void taskEnvois(void *pvParameters);

int envoyer(struct Donnees*);

// char* template = "{\"nom\": \"%s\", \"valeur\": \"%s\", \"dateCapture\": \"%s\", \"localisation\": \"%s\", \"description\": \"%s\", \"nomsa\": \"%s\"}";

const String nomsValeurs[] = {"temp", "hum", "co2", "lum", "pres"};

void testGetHttp();

#endif