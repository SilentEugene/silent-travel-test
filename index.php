<?php

require_once "Db.php";
require_once "./models/AbstractModel.php";
require_once "./models/CityModel.php";
require_once "./models/TravelerModel.php";

use Application\Db;
use Application\Model\CityModel;
use Application\Model\TravelerModel;

$db = new Db();

$db->init();

//$result = $db->addTraveler(new TravelerModel("Donald Trump"));
$result = $db->getTraveler(3);
print_r(json_encode($result));
$result = $db->getTravelers();
print_r(json_encode($result));

//$result = $db->getPlaces();
//print_r($result);

//$result = $db->getPlaces([1,3]);
//print_r($result);

//$result = $db->getVisitedCities(1);
//print_r($result);
//$result = $db->getVisitedCities(2);
//print_r($result);

//$result = $db->getTravelersInCity(1);
//print_r($result);
//$result = $db->getTravelersInCity(2);
//print_r($result);
//$result = $db->getTravelersInCity(3);
//print_r($result);
//$result = $db->getTravelersInCity(4);
//print_r($result);

//$db->ratePlace(1, 5, 6);
//$db->ratePlace(2, 5, 7);
//$db->ratePlace(3, 5, 3);

//$result = $db->getPlace(3);
//print_r($result);
//$result = $db->getCity(4);
//print_r($result);
//$result = $db->getTraveler(3);
//print_r($result);

//$result = $db->ratePlace(4, 8, 10);
//print_r($result);
//$db->ratePlace(3, 4, 2);
//$db->ratePlace(3, 6, 8);

//$result = $db->addCity("Madrid");
//print_r($result);
//$result = $db->addPlace("Royal Castle in Warsaw", 5, 3);
//print_r($result);
//$result = $db->addTraveler("Geralt of Rivia");
//print_r($result);

//$result = $db->getCities();
//print_r($result);
//$result = $db->getTravelers();
//print_r($result);
