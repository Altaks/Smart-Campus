#include "envois.h"

bool initEnvois(struct Donnees* donnees){
    return true;
}


int envoyer(struct Donnees* donnees){

    String date = getDate();
    
    char s_donnees[5][6];

    Serial.println("Serialisation des données");

    sprintf(s_donnees[0], ".2%f", donnees->tempEtHum->temperature);
    sprintf(s_donnees[1], ".2%f", donnees->tempEtHum->humidite);
    sprintf(s_donnees[2], "%d", donnees->co2);
    sprintf(s_donnees[3], "%d", donnees->lum);

    Serial.println("Obtention de la présence");

    if (*donnees->presence == 1){
        sprintf(s_donnees[4],  "true");
    }
    else{
        sprintf(s_donnees[4], "false");
    }

    Serial.println("Creation de la variable str d'envoi");

    uint8_t donneesAEnvoyer[256];

    Serial.println("Creation du client");

    WiFiClient client;

    HTTPClient http;
    /*
    curl -X 'POST' \
  'https://sae34.k8s.iut-larochelle.fr/api/captures' \
  -H 'accept: application/ld+json' \
  -H 'dbname: sae34bdm2eq3' \
  -H 'username: m2eq3' \
  -H 'userpass: howjoc-dyjhId-hiwre0' \
  -H 'Content-Type: application/json' \
  -d '{
  "nom": "hum",
  "valeur": "25.0",
  "dateCapture": "2023-12-21 09:25:00",
  "localisation": "C004",
  "description": "",
  "nomsa": "ESP-018"
}'
    */    

    Serial.println("Creation du header de la requette");

    http.addHeader("accept", "application/ld+json");
    http.addHeader("Content-Type", "application/json");
    http.addHeader("dbname", donnees->nomBD);
    http.addHeader("username", donnees->nomUtilisateur);
    http.addHeader("userpass", donnees->pwd);


    Serial.println("Connexion au serveur d'api");

    http.begin(client, "https://sae34.k8s.iut-larochelle.fr/api/captures");

    Serial.println("Verification de la connexion");
    
    if (http.connected()){
        Serial.println("Connexion réussie");
    }
    else{
        Serial.println("Connexion échouée");
        return 1;
    }

    
    

    Serial.println("Envoie de chaque donnees");

    for(unsigned short i = 0; i < 5; i++){
        size_t n;

        Serial.println("Creation des donnees de "+ nomsValeurs[i]);

        n = sprintf((char *) donneesAEnvoyer,
            "{\"nom\": \"%s\", \"valeur\": \"%s\", \"dateCapture\": \"%s\", \"localisation\": \"%s\", \"description\": \"%s\", \"nomsa\": \"%s\"}%c",
            nomsValeurs[i],
            s_donnees[i],
            date,
            donnees->salle,
            "",
            donnees->nomSa,
            EOF
            );

        Serial.println("Envoie des donnees");

        int codeReponse = http.POST(donneesAEnvoyer, strlen((char *) donneesAEnvoyer));

        Serial.println("Code de réponse :");
        Serial.println(codeReponse);
        String error = http.errorToString(codeReponse);
        Serial.println(error);

        Serial.println("Fin de l'envoie de "+ nomsValeurs[i]);
    }
   
    http.end();
    // Serial.println("Envoi des donnees");
    
    return 0;
}

void testGetHttp(){
    WiFiClient client;

    HTTPClient http;

    // get google.com

    http.begin(client, "https://www.google.com/");

    int httpCode = http.GET();

    if (httpCode > 0) {
        // HTTP header has been send and Server response header has been handled
        Serial.printf("[HTTP] GET... code: %d\n", httpCode);

        // file found at server
        if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_MOVED_PERMANENTLY) {
            String payload = http.getString();
            Serial.println(payload);
        }
    } else {
        Serial.printf("[HTTP] GET... failed, error: %s\n", http.errorToString(httpCode).c_str());
    }

    http.end();
}