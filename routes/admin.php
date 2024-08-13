<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\UserController;

Route::resource('/admin', UserController::class)->names('admin');