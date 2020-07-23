<?php

require_once "./vendor/autoload.php";

use Application\Application;

try {
    $app = new Application();
    echo $app->run();
} catch (Exception $e) {
    echo json_encode(array('error' => $e->getMessage()));
}
