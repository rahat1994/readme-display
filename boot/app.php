<?php

use ReadmeDisplay\Framework\Foundation\Application;
use ReadmeDisplay\App\Hooks\Handlers\ActivationHandler;
use ReadmeDisplay\App\Hooks\Handlers\DeactivationHandler;

return function($file) {

    $errorHandler = __DIR__ . "/error_handler.php";

    if (0 !== error_reporting() && file_exists($errorHandler)) {
        require_once $errorHandler;
    }

    $app = new Application($file);

    register_activation_hook($file, function() use ($app) {
        ($app->make(ActivationHandler::class))->handle();
    });

    register_deactivation_hook($file, function() use ($app) {
        ($app->make(DeactivationHandler::class))->handle();
    });

    add_action('plugins_loaded', function() use ($app) {
        do_action('readmedisplay_loaded', $app);
    });

    return $app;
};
