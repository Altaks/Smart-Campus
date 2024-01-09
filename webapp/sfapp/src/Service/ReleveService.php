<?php

namespace App\Service;

use App\Entity\SystemeAcquisition;
use DateTime;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Classe permettant de récupérer les relevés d'un système d'acquisition en utilisant l'API de SAE34
 * @package App\Service
 * @author Arnaud Mazurier
 * @version 1.0
 */
class ReleveService {

    /**
     * Permet de convertir un fichier JSON en relevés groupés par date avec une proximité temporelle de 5 minutes
     * @param array $array liste de relevés en JSON convertit par Symfony et la méthode toArray d'une réponse HTTP
     * @return array liste de relevés groupés par date avec une proximité de 5 minutes
     */
    private static function conversionVersRelevesGroupes(array $array) : array {
        $listeReleves = [];

        // Pour chaque relevé
        foreach($array as $releve){

            // Ajouter la date si elle n'existe pas déjà dans la liste des relevés
            if(!array_key_exists($releve["dateCapture"], $listeReleves)){
                $listeReleves[$releve["dateCapture"]] = ["temp" => null, "hum" => null, "co2" => null];
            }

            // Ajoute la valeur pour le timestamp exact
            $listeReleves[$releve["dateCapture"]][$releve["nom"]] = $releve["valeur"];
        }

        // Regrouper toutes les valeurs si elles sont à moins de 5 minutes d'écart
        $listeRelevesGroupes = [];

        // Pour chaque relevé
        foreach($listeReleves as $date => $releve){

            // Arrondi inférieur à la date qui est 5 minutes la plus proche
            $date = new DateTime($date);
            $date->setTime($date->format('H'), $date->format('i') - ($date->format('i') % 5), 0);
            $date = $date->format('Y-m-d H:i:s');

            // Ajouter la date si elle n'existe pas déjà dans la liste des relevés groupés
            if(!array_key_exists($date, $listeRelevesGroupes)){
                $listeRelevesGroupes[$date] = ["temp" => null, "hum" => null, "co2" => null];
            }

            // Ajouter les valeurs si elles n'existent pas déjà dans le relevé du timestamp donné (évite des collisions)
            if(is_null($listeRelevesGroupes[$date]["temp"])) $listeRelevesGroupes[$date]["temp"] = $releve["temp"];
            if(is_null($listeRelevesGroupes[$date]["hum"])) $listeRelevesGroupes[$date]["hum"] = $releve["hum"];
            if(is_null($listeRelevesGroupes[$date]["co2"])) $listeRelevesGroupes[$date]["co2"] = $releve["co2"];
        }

        return $listeRelevesGroupes;
    }

    /**
     * Permet de récupérer le dernier relevé d'un système d'acquisition
     * @param SystemeAcquisition $sa Le systeme d'acquisition dont on veut récupérer le dernier relevé
     * @return array Une array contenant le dernier relevé du système d'acquisition, converti
     */
    public function getDernier(SystemeAcquisition $sa) : array {

        // Création d'un client HTTP avec les informations de connexion
        $client = HttpClient::create([
            'headers' => [
                'accept' => 'application/json',
                'dbname' => $sa->getBaseDonnees(),
                'username' => 'm2eq3',
                'userpass' => 'howjoc-dyjhId-hiwre0'
            ]
        ]);

        // Envoi de la requête HTTP
        $response = $client->request('GET', 'https://sae34.k8s.iut-larochelle.fr/api/captures/last' , [
            'query' => ['page' => 1]
        ]);

        // Conversion de la réponse
        return static::conversionVersRelevesGroupes($response->toArray());
    }

    /**
     * Permet de récupérer tous les relevés d'un système d'acquisition
     * @param SystemeAcquisition $sa Le systeme d'acquisition dont on veut récupérer les relevés
     * @return array Une array contenant tous les relevés du système d'acquisition, convertis
     */
    public function getTout(SystemeAcquisition $sa) : array {

        // Création d'un client HTTP avec les informations de connexion
        $client = HttpClient::create([
            'headers' => [
                'accept' => 'application/json',
                'dbname' => $sa->getBaseDonnees(),
                'username' => 'm2eq3',
                'userpass' => 'howjoc-dyjhId-hiwre0'
            ]
        ]);


        // Envoi de la requête HTTP
        $response = $client->request('GET', 'https://sae34.k8s.iut-larochelle.fr/api/captures' , [
            'query' => ['page' => 1]
        ]);

        // Conversion de la réponse
        return static::conversionVersRelevesGroupes($response->toArray());
    }

    /**
     * @param SystemeAcquisition $sa Le systeme d'acquisition dont on veut récupérer les relevés
     * @param DateTime $dateDebut La date de début de la période, inclue, précision jusqu'au jour nécessaire
     * @param DateTime $dateFin La date de fin de la période, exclue, précision jusqu'au jour nécessaire
     * @return array Une array contenant tous les relevés du système d'acquisition qui sont apparus pendant la période donnée, convertis
     */
    public function getEntre(SystemeAcquisition $sa, DateTime $dateDebut, DateTime $dateFin) : array {

        // Création d'un client HTTP avec les informations de connexion
        $client = HttpClient::create([
            'headers' => [
                'accept' => 'application/json',
                'dbname' => $sa->getBaseDonnees(),
                'username' => 'm2eq3',
                'userpass' => 'howjoc-dyjhId-hiwre0'
            ]
        ]);

        // Envoi de la requête HTTP
        $response = $client->request('GET', 'https://sae34.k8s.iut-larochelle.fr/api/captures/interval' , [
            'query' => [
                'date1' => $dateDebut->format('Y-m-d'),
                'date2' => $dateFin->format('Y-m-d'),
                'page' => 1
            ]
        ]);

        // Conversion de la réponse
        return static::conversionVersRelevesGroupes($response->toArray());
    }

    public function verifierNomBaseDeDonnees(string $nomBase) : bool {

        // Création d'un client HTTP avec les informations de connexion
        $client = HttpClient::create([
            'headers' => [
                'accept' => 'application/json',
                'dbname' => $nomBase,
                'username' => 'm2eq3',
                'userpass' => 'howjoc-dyjhId-hiwre0'
            ]
        ]);

        // Envoi de la requête HTTP
        $response = $client->request('GET', 'https://sae34.k8s.iut-larochelle.fr/api/captures/last' , [
            'query' => ['page' => 1]
        ]);

        return $response->getStatusCode() == 200;
    }

}

?>