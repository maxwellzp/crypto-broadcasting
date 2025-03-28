<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

//echo "<pre>";
//var_dump($_ENV);
//echo "</pre>";
//die();

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
