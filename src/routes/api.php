<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReflectionController;

Route::apiResource('reflections', ReflectionController::class)->middleware('auth:sanctum');
