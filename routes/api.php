<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PrescriptionApiController;
use App\Http\Controllers\Api\VideoCallApiController;

Route::middleware(['auth:sanctum'])->group(function(){
    Route::get('/prescriptions', [PrescriptionApiController::class,'index']);
    Route::post('/prescriptions', [PrescriptionApiController::class,'store']);
    Route::post('/video-call-requests', [VideoCallApiController::class,'store']);
});

// public endpoints for registration/login managed by Fortify/Jetstream
