<?php

return [
    App\Providers\AppServiceProvider::class,
    ...class_exists(Laravel\Sanctum\SanctumServiceProvider::class)
        ? [Laravel\Sanctum\SanctumServiceProvider::class]
        : [],
];
