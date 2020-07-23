<?php

namespace Application\Model;

class PlaceModel extends AbstractModel
{
    private int $placeId;
    private string $placeName;
    private int $cityId;
    private string $cityName;
    private int $rate;
    private int $distance;

    public function __construct(
        string $placeName,
        int $cityId,
        int $distance,
        int $placeId = 0,
        string $cityName = '',
        int $rate = 0
    ) {
        $this->placeId = $placeId;
        $this->placeName = $placeName;
        $this->cityId = $cityId;
        $this->cityName = $cityName;
        $this->rate = $rate;
        $this->distance = $distance;
    }

    public function getPlaceId()
    {
        return $this->placeId;
    }

    public function getPlaceName()
    {
        return $this->placeName;
    }

    public function getCityId()
    {
        return $this->cityId;
    }

    public function getCityName()
    {
        return $this->cityName;
    }

    public function getRate()
    {
        return $this->rate;
    }

    public function getDistance()
    {
        return $this->distance;
    }

    public function jsonSerialize()
    {
        return [
            'placeId' => $this->getPlaceId(),
            'placeName' => $this->getPlaceName(),
            'cityId' => $this->getCityId(),
            'cityName' => $this->getCityName(),
            'rate' => $this->getRate(),
            'distance' => $this->getDistance()
        ];
    }
}
