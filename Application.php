<?php

namespace Application;

use RuntimeException;

class Application
{
    public $entities = ['place', 'traveler', 'city'];

    public $requestParams = [];

    protected $entity = '';
    protected $action = '';
    protected $method = '';

    private Controller $controller;

    public function __construct()
    {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

        $this->requestParams = $_REQUEST;

        if (!empty($this->requestParams['entity'])) {
            $this->entity = $this->requestParams['entity'];
        }
        if (!empty($this->requestParams['action'])) {
            $this->method = $this->requestParams['action'];
        } else if (!empty($this->requestParams['id'])) {
            $this->method = 'view';
        }

        $this->controller = new Controller();
    }

    public function run()
    {
        if (array_search($this->entity, $this->entities) === false) {
            throw new RuntimeException('API Not Found', 404);
        }

        $this->action = $this->getAction();

        if (method_exists($this, $this->action)) {
            return $this->{$this->action}();
        } else {
            throw new RuntimeException('Invalid Method', 405);
        }
    }

    protected function response($data, $status = 500)
    {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        return json_encode($data);
    }

    private function requestStatus($code)
    {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code]) ? $status[$code] : $status[500];
    }

    protected function getAction()
    {
        $method = $this->method;
        $entity = $this->entity;
        switch ($method) {
            case 'list':
                switch ($entity) {
                    case 'place':
                        return 'actionGetPlaces';
                    case 'traveler':
                        return 'actionGetTravelers';
                    case 'city':
                        return 'actionGetCities';
                    default:
                        return null;
                }
            case 'view':
                switch ($entity) {
                    case 'place':
                        return 'actionGetPlace';
                    case 'traveler':
                        return 'actionGetTraveler';
                    case 'city':
                        return 'actionGetCity';
                    default:
                        return null;
                }
            default:
                return null;
        }
    }

    private function actionGetPlaces()
    {
        if (!empty($this->requestParams['cities'])) {
            $cities = explode(',', $this->requestParams['cities']);
            return $this->response($this->controller->getPlaces($cities), 200);
        }
        if (!empty($this->requestParams['traveler'])) {;
            return $this->response($this->controller->getVisitedPlaces($this->requestParams['traveler']), 200);
        }
        return $this->response($this->controller->getPlaces(), 200);
    }

    private function actionGetCities()
    {
        if (!empty($this->requestParams['traveler'])) {;
            return $this->response($this->controller->getVisitedCities($this->requestParams['traveler']), 200);
        }
        return $this->response($this->controller->getCities(), 200);
    }

    private function actionGetTravelers()
    {
        if (!empty($this->requestParams['city'])) {;
            return $this->response($this->controller->getTravelersInCity($this->requestParams['city']), 200);
        }
        return $this->response($this->controller->getTravelers(), 200);
    }

    private function actionGetPlace() {
        return $this->response($this->controller->getPlace($this->requestParams['id']), 200);
    }

    private function actionGetCity() {
        return $this->response($this->controller->getCity($this->requestParams['id']), 200);
    }

    private function actionGetTraveler() {
        return $this->response($this->controller->getTraveler($this->requestParams['id']), 200);
    }
}
