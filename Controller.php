<?php

namespace Application;

use SQLite3;
use Application\Model\CityModel;
use Application\Model\PlaceModel;
use Application\Model\TravelerModel;

class Controller
{

    private SQLite3 $db;

    public function init()
    {
        $this->db = new SQLite3('db/travel.db');
    }

    public function getPlace(int $placeId)
    {
        $query = "SELECT places.*, cities.cityName
        FROM places, cities WHERE placeId = :placeId AND cities.cityId = places.cityId
        GROUP BY places.placeName";
        $result = $this->runQueryWithParam($query, [':placeId' => $placeId], true);
        if ($result === false || isset($result['error']) || isset($result['code'])) {
            return false;
        }
        $rates = $this->getRates([$placeId]);
        $rate = isset($rates[$placeId]) ? $rates[$placeId]['placeRate'] : 0;
        return new PlaceModel(
            $result['placeName'],
            $result['cityId'],
            $result['distance'],
            $result['placeId'],
            $result['cityName'],
            $rate
        );
    }

    public function getCity(int $cityId)
    {
        $query = "SELECT * FROM cities WHERE cityId = :cityId";
        $result = $this->runQueryWithParam($query, [':cityId' => $cityId], true);
        if ($result === false || isset($result['error']) || isset($result['code'])) {
            return false;
        }
        return new CityModel($result['cityName'], $result['cityId']);
    }

    public function getTraveler(int $travelerId)
    {
        $query = "SELECT * FROM travelers WHERE travelerId = :travelerId";
        $result = $this->runQueryWithParam($query, [':travelerId' => $travelerId], true);
        if ($result === false || isset($result['error']) || isset($result['code'])) {
            return false;
        }
        $cities = $this->getVisitedCities($travelerId);
        if ($cities === false) $cities = [];
        return new TravelerModel($result['name'], $result['travelerId'], $cities);
    }

    public function addCity(CityModel $city)
    {
        $query = "INSERT INTO cities(cityName) VALUES (:cityName)";
        $result = $this->runQueryWithParam($query, [':cityName' => $city->getCityName()]);
        return $result;
    }

    public function addTraveler(TravelerModel $traveler)
    {
        $query = "INSERT INTO travelers(name) VALUES (:travelerName)";
        $result = $this->runQueryWithParam($query, [':travelerName' => $traveler->getName()]);
        return $result;
    }

    public function addPlace(PlaceModel $place)
    {
        $query = "INSERT INTO places(placeName, cityId, distance) VALUES (?, ?, ?)";
        $result = $this->runQueryWithParam($query, [
            1 => $place->getPlaceName(),
            2 => $place->getCityId(),
            3 => $place->getDistance()
        ]);
        return $result;
    }

    public function getPlaces(array $cities = [])
    {
        if (!empty($cities)) {
            $ids = implode(",", $cities);
            $query = "SELECT * FROM places 
            INNER JOIN cities ON cities.cityId IN ($ids) AND places.cityId = cities.cityId
            ORDER BY cityName, placeName";
        } else {
            $query = "SELECT places.*, cities.cityName
            FROM places, cities WHERE cities.cityId = places.cityId
            GROUP BY places.placeName";
        }
        $places = $this->runQuery($query);
        $rates = $this->getRates();
        foreach ($places as &$place) {
            $place['placeRate'] = isset($rates[$place['placeId']]) ? $rates[$place['placeId']]['placeRate'] : 0;
        }
        unset($place);
        $data = [];
        foreach ($places as $place) {
            $data[] = new PlaceModel(
                $place['placeName'],
                $place['cityId'],
                $place['distance'],
                $place['placeId'],
                $place['cityName'],
                $place['placeRate']
            );
        }
        return $data;
    }

    public function getCities()
    {
        $query = "SELECT * FROM cities ORDER BY cityName";
        $result = $this->runQuery($query);
        if ($result === false || isset($result['error']) || isset($result['code'])) {
            return false;
        }
        $data = [];
        foreach ($result as $city) {
            $data[] = new CityModel($city['cityName'], $city['cityId']);
        }
        return $data;
    }

    public function getTravelers()
    {
        $query = "SELECT * FROM travelers ORDER BY name";
        $result = $this->runQuery($query);
        if ($result === false || isset($result['error']) || isset($result['code'])) {
            return false;
        }
        $data = [];
        foreach ($result as $traveler) {
            $cities = $this->getVisitedCities($traveler['travelerId']);
            if ($cities === false) $cities = [];
            $places = $this->getVisitedPlaces($traveler['travelerId']);
            if ($places === false) $places = [];
            $data[] = new TravelerModel($traveler['name'], $traveler['travelerId'], $cities, $places);
        }
        return $data;
    }

    public function getVisitedCities(int $travelerId)
    {
        $query = "SELECT * FROM cities
            INNER JOIN (SELECT * FROM visits WHERE travelerId = :travelerId) USING (cityId)
            ORDER BY cityName";
        $result = $this->runQueryWithParam($query, [':travelerId' => $travelerId]);
        if ($result === false || isset($result['error']) || isset($result['code'])) {
            return false;
        }
        $data = [];
        foreach ($result as $city) {
            $data[] = new CityModel($city['cityName'], $city['cityId']);
        }
        return $data;
    }

    public function getVisitedPlaces(int $travelerId)
    {
        $query = "SELECT places.*, cities.cityName, rates.rate
            FROM places, cities, rates
            WHERE rates.travelerId = :travelerId AND places.placeId = rates.placeId AND cities.cityId = places.cityId
            ORDER BY cityName, placeName";
        $result = $this->runQueryWithParam($query, [':travelerId' => $travelerId]);
        if ($result === false || isset($result['error']) || isset($result['code'])) {
            return false;
        }
        $places = [];
        foreach ($result as $place) {
            $places[] = new PlaceModel(
                $place['placeName'],
                $place['cityId'],
                $place['distance'],
                $place['placeId'],
                $place['cityName'],
                $place['rate']
            );
        }
        return $places;
    }

    public function getTravelersInCity(int $cityId)
    {
        $query = "SELECT * FROM travelers
            INNER JOIN (SELECT * FROM visits WHERE cityId = :cityId) USING (travelerId)
            ORDER BY travelers.name";
        return $this->runQueryWithParam($query, [':cityId' => $cityId]);
    }

    private function getRates(array $placeIds = [])
    {
        if (empty($placeIds)) {
            $query = "SELECT placeId, CAST (AVG(rate) AS INTEGER) AS placeRate
            FROM rates GROUP BY placeId";
        } else {
            $ids = implode(",", $placeIds);
            $query = "SELECT placeId, CAST (AVG(rate) AS INTEGER) AS placeRate
            FROM rates WHERE placeId IN ($ids) GROUP BY placeId";
        }
        return $this->runQuery($query, 'placeId');
    }

    public function ratePlace(int $travelerId, int $placeId, int $rate)
    {
        $query = "INSERT INTO rates(travelerId, placeId, rate) VALUES (?, ?, ?)";
        $result = $this->runQueryWithParam($query, [
            1 => $travelerId,
            2 => $placeId,
            3 => $rate
        ]);

        if (isset($result['error'])) {
            return $result;
        }

        if (isset($result['status']) && $result['status'] == 200) {
            $place = $this->getPlace($placeId);
            print_r('Место:' . PHP_EOL);
            print_r($place);
            if (!empty($place)) {
                $result = $this->checkInCity($travelerId, $place['cityId']);
            }
            if (isset($result['error'])) {
                return $result;
            }
        }
    }

    public function checkInCity(int $travelerId, int $cityId)
    {
        $query = "INSERT INTO visits(travelerId, cityId) VALUES (?, ?)";
        return $this->runQueryWithParam($query, [1 => $travelerId, 2 => $cityId]);
    }

    private function runQuery(string $query, string $fieldAsKey = null)
    {
        $result = $this->db->query($query);

        if ($result == false) {
            return [
                'error' =>  $this->db->lastErrorMsg()
            ];
        }

        if ($result === true || $result->numColumns() == 0) {
            return [
                'status' => 200
            ];
        } else {
            $data = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                if (empty($fieldAsKey)) {
                    $data[] = $row;
                } else {
                    $data[$row[$fieldAsKey]] = $row;
                }
            }
            return $data;
        }
    }

    private function runQueryWithParam(string $query, array $params, bool $single = false)
    {
        $stmt = $this->db->prepare($query);
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        $result = $stmt->execute();

        if ($result == false) {
            return [
                'error' =>  $this->db->lastErrorMsg()
            ];
        }

        if ($result->numColumns() == 0) {
            return [
                'status' => 200
            ];
        } else {
            if ($single) {
                return $result->fetchArray(SQLITE3_ASSOC);
            }

            $data = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }
    }
}
