<?php

namespace Application\Model;

class CityModel extends AbstractModel
{
    private int $cityId;
    private string $cityName;

    public function __construct(string $cityName, int $cityId = 0) {
        $this->cityName = $cityName;
        $this->cityId = $cityId;
    }

    public function getId()
    {
        return $this->cityId;
    }

    public function getCityName()
    {
        return $this->cityName;
    }

    public function jsonSerialize()
    {
        return [
            'cityId' => $this->cityId,
            'cityName' => $this->cityName
        ];
    }
}
