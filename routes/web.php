<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
//    return [
//        'Laravel' => app()->version(),
//        'PHP' => phpversion(),
//    ];
    return [
        'Bienvenida' => 'Bienvenido a la API de finanzas personales',
    ];
});


