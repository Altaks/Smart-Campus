<?php

namespace App\Service;

use DateTime;
use Symfony\Component\Validator\Constraints\Date;

class ReleveService {

    public function getDernier(int $tag) : array{
        $a_dir = getcwd();

        chdir("/app/sfapp/releves");



        $fileName = "{$tag}.json";

        $file = file_get_contents($fileName);

        if (!file_exists($fileName)){
            return ["date" => null, "co2" => null, "temp" => null, "hum" => null];
        }

        $json = json_decode($file, true);

        $valuesFetch = ["date" => null, "co2" => null, "temp" => null, "hum" => null];
        $valuesRead = ["date" => false, "co2" => false, "temp" => false, "hum" => false];


        foreach ($json as $releve){
            $type = $releve["nom"];
            if (!$valuesRead[$type]){

                if (!$valuesRead["date"]){
                    $valuesFetch[$type] = $releve["valeur"];



                    $valuesFetch["date"] = $releve["dateCapture"];
                    $valuesRead[$type] = true;
                    $valuesRead["date"] = true;
                }
                else{

                    $date1 = new DateTime($valuesFetch["date"]);
                    $date2 = new DateTime($releve["dateCapture"]);
                    $diff = $date1->diff($date2);

                    if (!($diff->y != 0 || $diff->m != 0 || $diff->d != 0 || $diff->h != 0) && $diff->i < 5 && $diff->i > -5){
                        $valuesFetch[$type]=$releve["valeur"];
                        $valuesRead[$type]=true;
                    }
                }
            }
            if ($valuesRead["date"] && $valuesRead["co2"] && $valuesRead["temp"] && $valuesRead["hum"]){
                break;
            }
        }
        chdir($a_dir);
        return $valuesFetch;
    }

    public function getAll(int $tag) : array{
        $a_dir = getcwd();

        chdir("/app/sfapp/releves");



        $fileName = "{$tag}.json";

        $file = file_get_contents($fileName);

        if (!file_exists($fileName)){
            return [];
        }

        $json = json_decode($file, true);
        $releves = array();

        foreach ($json as $releve){
            $releves[] = ['date'=>$releve['dateCapture'], 'type'=> $releve['nom'], 'valeur'=>$releve['valeur']];
        }

        chdir($a_dir);
        return $releves;
    }

}

?>