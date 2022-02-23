<?php

use App\Http\Controllers\HostReservationController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\OfficeImageController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserReservationController;
use Illuminate\Support\Facades\Route;


Route::get('/tags', TagController::class);
Route::get('/offices', [OfficeController::class, 'index']);
Route::get('/offices/{office}', [OfficeController::class, 'show']);


Route::post('/offices', [OfficeController::class, 'create'])->middleware(['auth:sanctum', 'verified']);
Route::put('/offices/{office}', [OfficeController::class, 'update'])->middleware(['auth:sanctum', 'verified']);
Route::delete('/offices/{office}', [OfficeController::class, 'delete'])->middleware(['auth:sanctum', 'verified']);

Route::post('/offices/{office}/images', [OfficeImageController::class, 'store'])->middleware(['auth:sanctum', 'verified']);
Route::delete('/offices/{office}/images/{image:id}', [OfficeImageController::class, 'delete'])->middleware(['auth:sanctum', 'verified']);

// user Reservations...
Route::get('/reservations', [UserReservationController::class , 'index'])->middleware(['auth:sanctum', 'verified']);
Route::post('/reservations/{office}', [UserReservationController::class , 'store'])->middleware(['auth:sanctum', 'verified'])->name('reservation-user.store');

// host Reservations...
Route::get('/host/reservations', [HostReservationController::class , 'index']);
