<?php

namespace App\Service;

use App\Entity\SystemeAcquisition;
use DateTime;
use Symfony\Component\HttpClient\HttpClient;

class ReleveService {

    private static function conversionVersRelevesGroupes(array $array) : array {
        $listeReleves = [];

        foreach($array as $releve){
            if(!array_key_exists($releve["dateCapture"], $listeReleves)){
                $listeReleves[$releve["dateCapture"]] = ["temp" => null, "hum" => null, "co2" => null];
            }
            $listeReleves[$releve["dateCapture"]][$releve["nom"]] = $releve["valeur"];
        }
        return $listeReleves;
    }

    public function getDernier(SystemeAcquisition $sa) : array {
        $client = HttpClient::create([
            'headers' => [
                'accept' => 'application/json',
                'dbname' => $sa->getBaseDonnees(),
                'username' => 'm2eq3',
                'userpass' => 'howjoc-dyjhId-hiwre0'
            ]
        ]);

        $response = $client->request('GET', 'https://sae34.k8s.iut-larochelle.fr/api/captures/last' , [
            'query' => ['page' => 1]
        ]);
        return static::conversionVersRelevesGroupes($response->toArray());
    }

    public function getTout(SystemeAcquisition $sa) : array{
        $client = HttpClient::create([
            'headers' => [
                'accept' => 'application/json',
                'dbname' => $sa->getBaseDonnees(),
                'username' => 'm2eq3',
                'userpass' => 'howjoc-dyjhId-hiwre0'
            ]
        ]);

        $response = $client->request('GET', 'https://sae34.k8s.iut-larochelle.fr/api/captures' , [
            'query' => ['page' => 1]
        ]);
        return static::conversionVersRelevesGroupes($response->toArray());
    }

    public function getEntre(SystemeAcquisition $sa, DateTime $dateDebut, DateTime $dateFin) : array{
        $client = HttpClient::create([
            'headers' => [
                'accept' => 'application/json',
                'dbname' => $sa->getBaseDonnees(),
                'username' => 'm2eq3',
                'userpass' => 'howjoc-dyjhId-hiwre0'
            ]
        ]);

        $response = $client->request('GET', 'https://sae34.k8s.iut-larochelle.fr/api/captures/interval' , [
            'query' => [
                'date1' => $dateDebut->format('Y-m-d'),
                'date2' => $dateFin->format('Y-m-d'),
                'page' => 1
            ]
        ]);
        return static::conversionVersRelevesGroupes($response->toArray());
    }

}

?>