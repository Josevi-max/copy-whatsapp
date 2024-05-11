<?php

use App\Http\Controllers\ContactController;
use App\Livewire\ChatComponent;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('contacts', ContactController::class)->middleware('auth:sanctum')->except('show');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('chat', ChatComponent::class)->middleware("auth:sanctum")->name("chats.index");