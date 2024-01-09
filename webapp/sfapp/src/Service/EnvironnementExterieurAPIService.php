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
class EnvironnementExterieurAPIService {

    public const openweatherEndpoint = "https://api.open-meteo.com/v1/forecast?";

    public const latitude = 46.1631;
    public const longitude = -1.1522;

    public function queryDailyTempsAndHumidity() : array | null
    {
        // Création d'un client HTTP avec les informations de connexion
        $client = HttpClient::create();

        $now = new DateTime();
        $yesterday = new DateTime("-1 day");

        // Envoi de la requête HTTP
        $response = $client->request('GET', self::openweatherEndpoint , [
            'query' => [
                'latitude' => self::latitude,
                'longitude' => self::longitude,
                'hourly' => 'temperature_2m,relative_humidity_2m',
                'timeformat' => 'unixtime',
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $now->format('Y-m-d')
            ]
        ]);

        if(!json_validate($response->getContent())){
            // value is not a valid JSON string, invalid response
            return null;
        }

        // Récupération du contenu de la réponse HTTP
        $content = json_decode($response->getContent());

        // Conversion du JSON en tableau PHP
        $timetamps_data = $content->hourly->time;
        $temps_data_data = $content->hourly->temperature_2m;
        $humidity_data_data = $content->hourly->relative_humidity_2m;

        // Récupération des relevés groupés par date s'ils sont dans une fenêtre de 24h
        for($i = 0; $i < count($timetamps_data); $i++){

            $timestamp_date = new DateTime('@' . $timetamps_data[$i]);
            if(date_diff($now, $timestamp_date)->d < 0 || $timestamp_date > $now){

                $timetamps[] = $timetamps_data[$i];
                $temps_data[] = $temps_data_data[$i];
                $humidity_data[] = $humidity_data_data[$i];

            }
        }

        // Retourne les relevés groupés par dates
        return [
            "timestamps" => $timetamps,
            "temps" => $temps_data,
            "humidity" => $humidity_data
        ];
    }

}

?>