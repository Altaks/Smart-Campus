#include "SPIFFS.h"

#include "captiveRequestHandler.h"

bool CaptiveRequestHandler::canHandle(AsyncWebServerRequest *request)
{
    return true;
}

void CaptiveRequestHandler::handleRequest(AsyncWebServerRequest *request)
{
    request->redirect("http://"+WiFi.softAPIP().toString()+"/");
}