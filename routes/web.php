<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;


Route::controller(StudentController::class)->group(function(){

Route::get('/', 'index')->name('index');
Route::post('/upload', 'upload')->name('upload');
Route::get('/search', 'searchIndex')->name('search');
Route::get('/search-data', 'search');

});

