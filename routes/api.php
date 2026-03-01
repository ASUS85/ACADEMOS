<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/filieres/{department}', function ($department) {

        return \App\Models\Filiere::where('department_id', $department)->get();
    });
});
