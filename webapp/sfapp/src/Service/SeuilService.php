<?php

namespace App\Service;

use App\Entity\Seuil;
use App\Repository\SeuilRepository;

/**
 * Classe permettant d'intéragir les seuils dans l'api
 * @package App\Service
 * @author Adrien Panis
 * @version 1.0
 */
class SeuilService {

    /**
     * Permet de recuperer les seuil dans la base de données
     * @return array map de tout les seuils avec le nom en clé et la valeur en valeur
     */
    public static function getSeuils() : array {
        $seuilRepository = static::getContainer()->get(SeuilRepository::class);
        $listeSeuils = $seuilRepository->findAll();
        $mapSeuil = [];
        foreach ($listeSeuils as $seuil)
        {
            $mapSeuil[$seuil->getNom()] = $seuil->getValeur();
        }
        return $mapSeuil;
    }

}

?>