<?php

namespace Application\Model;

class TravelerModel extends AbstractModel
{
    private int $travelerId;
    private string $name;
    
    public function __construct(string $name, int $travelerId = 0) {
        $this->name = $name;
        $this->travelerId = $travelerId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->travelerId;
    }

    public function jsonSerialize()
    {
        return [
            'travelerId' => $this->travelerId,
            'name' => $this->name
        ];
    }
}
