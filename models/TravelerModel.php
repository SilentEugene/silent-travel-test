<?php

namespace Application\Model;

class TravelerModel extends AbstractModel
{
    private int $travelerId;
    private string $name;
    private array $cities;
    private array $places;

    public function __construct(
        string $name,
        int $travelerId = 0,
        array $cities = [],
        array $places = []
    ) {
        $this->name = $name;
        $this->travelerId = $travelerId;
        $this->cities = $cities;
        $this->places = $places;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->travelerId;
    }

    public function getCities()
    {
        return $this->cities;
    }

    public function getPlaces()
    {
        return $this->places;
    }

    public function jsonSerialize()
    {
        return [
            'travelerId' => $this->travelerId,
            'name' => $this->name,
            'cities' => $this->cities,
            'places' => $this->places
        ];
    }
}
