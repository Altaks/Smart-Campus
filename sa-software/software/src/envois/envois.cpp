#include "envois.h"


// Décommenter/Commenter les Serial.println pour voir/ne pas voir les informations de debug en usb

struct Donnees * donnees_ptr = nullptr;

void taskEnvois(void *pvParameters){
    while(1){
        Serial.println("______________________________________");
        Serial.println("Debut de l'envoie des donnees :");
        Serial.println("______________________________________");
        int codeRetour = envoyer(donnees_ptr);
        if (codeRetour == 0){
            Serial.println("Donnees envoyees");
        }
        else{
            Serial.println("Erreur lors de l'envoi des donnees");
            Serial.println("Code d'erreur : " + String(codeRetour) + " : " + errreurToString(codeRetour));
        }
        Serial.println("______________________________________");
        // 5 minutes - 2 secondes pour laisser le temps à la tâche de récupérer la date (d'après mes tests, la récupération de la date prend 2 secondes de plus que le délai de 5 minutes)
        vTaskDelay(pdMS_TO_TICKS(5 * 60 * 1000 - 2 * 1000)); 
    }
}

bool initEnvois(struct Donnees* donnees){

    donnees_ptr = donnees;

    xTaskHandle envoisTaskHandle;

    xTaskCreate( //création de la tâche
      taskEnvois,
      "taskEnvois",
      10000,
      NULL,
      1,
      &envoisTaskHandle
    );

    if(envoisTaskHandle == NULL){
        return false;
    }

    return true;
}

int envoyer(struct Donnees* donnees){
    
    Serial.println("Vériication de la connexion au réseau");
    // verification de la connexion au réseau
    if (!estConnecte()){
        return -2;
    }

    Serial.println("Obtention de la date");
    String date = getDate();

    // vérification de la date
    if (date == "Date Error"){
        return -1;
    }

    char s_donnees[5][6];

    Serial.println("Serialisation des données récupérées");
    
    // recupérer les données
    sprintf(s_donnees[0], "%.2f", donnees->tempEtHum->temperature); // %2.f pour  2 chiffres après la virgule
    sprintf(s_donnees[1], "%2.f", donnees->tempEtHum->humidite); // %2.f pour avoir 2 chiffres après la virgule
    sprintf(s_donnees[2], "%hu", *donnees->co2); // %hu pour avoir un unsigned short
    sprintf(s_donnees[3], "%hu", *donnees->lum); // %hu pour avoir un unsigned short

    // presence est un booléen, on le converti en string
    if (*donnees->presence == 1){
        sprintf(s_donnees[4],  "true");
    }
    else{
        sprintf(s_donnees[4], "false");
    }


    Serial.println("Creation du client");

    // création du client http pour envoyer les données
    HTTPClient http;


    // requete POST basée sur l'exemple de l'api suivant :
    /*
    curl -X 'POST' \
    'https://sae34.k8s.iut-larochelle.fr/api/captures' \
    -H 'accept: application/ld+json' \
    -H 'dbname: sae34bdm2eq3' \
    -H 'username: m2eq3' \
    -H 'userpass: <pwd>' \
    -H 'Content-Type: application/json' \
    -d '{
    "nom": "<nom>",
    "valeur": "<valeur>",
    "dateCapture": "<dateCapture>",
    "localisation": "<localisation>",
    "description": "",
    "nomsa": "<nomsa>"
    }'
    */    

    // Décommenter pour avoir les données de connexion à l'api
    // Serial.println("Donnees de connexion :");
    // Serial.println(donnees->nomBD);
    // Serial.println(donnees->nomUtilisateurBD);
    // Serial.println(donnees->pwd);

    // met un timeout à 7 seconds
    http.setTimeout(7000);

    Serial.println("Connexion au serveur d'api");

    // configure la connexion au serveur d'api (changer l'url si besoin)
    http.begin("https://sae34.k8s.iut-larochelle.fr/api/captures");
    
    Serial.println("Creation du header de la requete");

    // configure le header de la requete
    http.addHeader("accept", "application/ld+json");
    http.addHeader("dbname", donnees->nomBD);
    http.addHeader("username", donnees->nomUtilisateurBD);
    http.addHeader("userpass", donnees->pwd);
    http.addHeader("Content-Type", "application/json");


    Serial.println("Envoie de chaque donnees");

    for(unsigned short i = 0; i < 5; i++){
        size_t n;

        Serial.println("Sérialisation des donnees de "+ nomsValeurs[i]);

        // Création de la chaine de caractère à envoyer
        String donneesAEnvoyerStr = "{\"nom\":\""+ nomsValeurs[i] +"\",\"valeur\":\""+ s_donnees[i] +"\",\"dateCapture\":\""+ date +"\",\"localisation\":\""+ donnees->salle +"\",\"description\":\"\",\"nomsa\":\""+ donnees->nomSa +"\"}";

        Serial.println("Envoie des donnees");

        // Envoie des données
        int codeReponse = http.POST(donneesAEnvoyerStr);

        // affiche le code de reponse
        if (http.errorToString(codeReponse) != ""){
            Serial.println("Code de reponse : " + String(codeReponse) + " : " + http.errorToString(codeReponse));
        }
        else{
            Serial.println("Code de reponse : " + String(codeReponse));
        }
    }
    
    // libère les ressources
    http.end();
    return 0;
}

String errreurToString(int code){
    switch (code)
    {
    case -1:
        return "Erreur de date";
        break;
    case -2:
        return "Erreur de connexion";
        break;
    default:
        return "Erreur inconnue";
    }
}