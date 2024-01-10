#ifndef CAPTIVEREQUESTHANDLER_H
#define CAPTIVEREQUESTHANDLER_H

#include <ESPAsyncWebServer.h>


/**
 * Classe CaptiveRequestHandler
 * 
 * Cette classe permet de gérer les requêtes HTTP
 * 
 * @param request
 * @return true
 * @return false
 */
class CaptiveRequestHandler : public AsyncWebHandler {
public:
    CaptiveRequestHandler(){}
    virtual ~CaptiveRequestHandler(){}

    /**
     * Cette fonction permet de savoir si l'on peut gérer les requêtes HTTP entrées
     * @return true car on utilise cette classe pour gérer toutes les requêtes HTTP entrantes
    */
    bool canHandle(AsyncWebServerRequest *request);

    void handleRequest(AsyncWebServerRequest *request);
};

#endif