<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\VideoCallRequestController;

Route::get('/', function () { return inertia('Dashboard'); })->name('home');

// Auth routes provided by Jetstream/Fortify (install Jetstream separately)

// Prescriptions
Route::middleware(['auth'])->group(function(){
    Route::get('/prescriptions', [PrescriptionController::class,'index'])->name('prescriptions.index');
    Route::get('/prescriptions/create', [PrescriptionController::class,'create'])->name('prescriptions.create');
    Route::post('/prescriptions', [PrescriptionController::class,'store'])->name('prescriptions.store');
    Route::get('/prescriptions/{id}', [PrescriptionController::class,'show'])->name('prescriptions.show');
    Route::patch('/prescriptions/{id}/review', [PrescriptionController::class,'review'])->middleware('role:DOCTOR')->name('prescriptions.review');

    Route::post('/video-call-requests', [VideoCallRequestController::class,'store'])->middleware('can:write-prescription')->name('video.requests.store');
});

// Admin
Route::prefix('admin')->middleware(['auth','role:ADMIN'])->group(function(){
    Route::get('/users', [UserManagementController::class,'index']);
    Route::patch('/users/{id}/approve', [UserManagementController::class,'approve']);
    Route::patch('/users/{id}/suspend', [UserManagementController::class,'suspend']);
    Route::patch('/users/{id}/permission', [UserManagementController::class,'togglePermission']);
    Route::resource('medicines', MedicineController::class);
    Route::post('/assignments', [UserManagementController::class,'assign']);
});
