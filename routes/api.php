<?php

use Illuminate\Support\Facades\Route;

Route::get('/jobs', 'App\Http\Controllers\JobController@index');
