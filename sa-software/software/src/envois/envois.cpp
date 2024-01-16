#include "envois.h"

#include "WiFi.h"

#include "Fichiers/fichierSPIFFS.h"
#include "Capteurs/presence.h"
#include "Capteurs/tempEtHum.h"
#include "Capteurs/qualAir.h"

// Décommenter/Commenter les Serial.println pour voir/ne pas voir les informations de debug en usb

void taskEnvois(void *pvParameters){
    while(1){
        vTaskDelay(pdMS_TO_TICKS(30 * 1000));
        Serial.println("______________________________________");
        Serial.println("Debut de l'envoie des donnees :");
        Serial.println("______________________________________");
        int codeRetour = envoyer();
        if (codeRetour == 0){
            Serial.println("Donnees envoyees");
        }
        else{
            Serial.println("Erreur lors de l'envoi des donnees");
            Serial.println("Code d'erreur : " + String(codeRetour) + " : " + errreurToString(codeRetour));
        }
        Serial.println("______________________________________");
        // 5 minutes - 2 secondes pour laisser le temps à la tâche de récupérer la date (d'après mes tests, la récupération de la date prend 2 secondes de plus que le délai de 5 minutes)
        vTaskDelay(pdMS_TO_TICKS(4 * 60 * 1000 + 28 * 1000));
    }
}

bool initEnvois(){

    xTaskHandle envoisTaskHandle;

    xTaskCreate( //création de la tâche
      taskEnvois,
      "Envois des donnees sur l'api",
      8000,
      NULL,
      1,
      &envoisTaskHandle
    );

    if(envoisTaskHandle == NULL){
        return false;
    }

    return true;
}

int envoyer(){
    
    Serial.println("Vériication de la connexion au réseau");
    // verification de la connexion au réseau
    if (WiFi.status() != WL_CONNECTED){
        abort(); // redemarre l'esp pour se reconnecter au réseau
    }

    Serial.println("Obtention de la date");
    String date = getDate();

    // vérification de la date
    if (date == "Date Error"){
        return -1;
    }

    char s_donnees[4][6];

    Serial.println("Serialisation des données récupérées");
    
    // recupérer les données
    // si une donnée n'est pas disponible, on met une chaine de caractère vide
    getTemperature() == -1 ? sprintf(s_donnees[0], "%c", EOF) : sprintf(s_donnees[0], "%.2f", getTemperature()); //donnees->tempEtHum->temperature); // %2.f pour  2 chiffres après la virgule
    getHumidite() == -1 ? sprintf(s_donnees[1], "%c", EOF) : sprintf(s_donnees[1], "%2.f", getHumidite()); //donnees->tempEtHum->humidite); // %2.f pour avoir 2 chiffres après la virgule
    getCO2() == 0 ? sprintf(s_donnees[2], "%c", EOF) : sprintf(s_donnees[2], "%hu", getCO2()); //*donnees->co2); // %hu pour avoir un unsigned short

    // presence est un booléen, on le converti en string
    if (getPresence()){//*donnees->presence == 1){
        sprintf(s_donnees[3],  "true");
    }
    else{
        sprintf(s_donnees[3], "false");
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
    // Serial.println(recupererValeur("/infobd.txt","nom_bd").c_str());
    // Serial.println(recupererValeur("/infobd.txt","nom_utilisateur").c_str());
    // Serial.println(recupererValeur("/infobd.txt","mot_de_passe").c_str());

    Serial.println("Connexion au serveur d'api");

    // configure la connexion au serveur d'api (changer l'url si besoin)
    http.begin("https://sae34.k8s.iut-larochelle.fr/api/captures");
    
    Serial.println("Creation du header de la requete");

    String nomSA = recupererValeur("/infobd.txt","nom_sa").c_str();
    String nomBd = recupererValeur("/infobd.txt","nom_bd").c_str();
    String nomUtilisateur = recupererValeur("/infobd.txt","nom_utilisateur").c_str();
    String motDePasse = recupererValeur("/infobd.txt","mot_de_passe").c_str();
    

    if(nomBd.isEmpty() || nomUtilisateur.isEmpty() || motDePasse.isEmpty() || nomSA.isEmpty()){
        return -2;
    }
    

    // configure le header de la requete
    http.addHeader("accept", "application/ld+json");
    http.addHeader("dbname", nomBd);
    http.addHeader("username", nomUtilisateur);
    http.addHeader("userpass", motDePasse);
    http.addHeader("Content-Type", "application/json");

    Serial.println("Envoie de chaque donnees");

    int codeErreur = 0;

    for(unsigned short i = 0; i < 4; i++){

        Serial.printf("Récupération des données de %s\n", nomsValeurs[i]);

        while(strlen(s_donnees[i]) == 1 && i < 4){
            i++;
            Serial.printf("Erreur lors de la récupération des données.\nRécupération des données de %s\n", nomsValeurs[i]);
        }

        // Création de la chaine de caractère à envoyer
        String donneesAEnvoyerStr = "{\"nom\":\""+ nomsValeurs[i] +
                                    "\",\"valeur\":\""+ s_donnees[i] +
                                    "\",\"dateCapture\":\""+ date +
                                    "\",\"localisation\":\""+ recupererValeur("/infobd.txt", "localisation").c_str() +
                                    "\",\"description\":\"\",\"nomsa\":\""+ recupererValeur("/infobd.txt", "nom_sa").c_str() +
                                    "\"}";

        Serial.println("Envoie des donnees");

        // Décommenter pour avoir la donnée envoyée à l'api
        // Serial.println(donneesAEnvoyerStr);

        // Décommenter pour voir la mémoire libre sur l'esp
        Serial.println("Memoire RAM restante : " + String(ESP.getFreeHeap()) + "o");

        // Envoie des données
        int codeReponse = http.POST(donneesAEnvoyerStr);

        // affiche le code de reponse
        if (http.errorToString(codeReponse) != ""){
            Serial.println("Code de reponse : " + String(codeReponse) + " : " + http.errorToString(codeReponse));
        }
        else{
            Serial.println("Code de reponse : " + String(codeReponse));
        }

        if (codeReponse != 201){
            codeErreur = -4;
        }

    }
    
    // libère les ressources
    http.end();

    return codeErreur;
}

String errreurToString(int code){

    switch (code)
    {
    case -1:
        return "Erreur de date";
    case -2:
        return "Erreur lors de la récupération des données de connexion à la base de données";
    case -3:
        return "Données non disponibles";
    case -4:
        return "Erreur lors de l'envoi des données (requette http)";
    default:
        return "Erreur inconnue";
    }
}