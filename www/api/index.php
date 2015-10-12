<?php

require __DIR__.'/../../vendor/autoload.php';

$app = new \Slim\Slim();

$app->get('/version', function () {
    echo "0.0.0";
});

$app->run();
