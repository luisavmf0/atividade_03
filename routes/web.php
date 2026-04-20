<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/teste-db', function () {
    return "Banco conectado: " . DB::connection()->getDatabaseName();
});