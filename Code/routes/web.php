<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SupportTicketController;


//home page
Route::get('/', function () {
    return view('ticket');
})->name('ticket');

//auth route
Auth::routes();

//home
Route::get('/home', [HomeController::class, 'index'])->name('home');
//tickets 
Route::get('/tickets_data', [SupportTicketController::class, 'get_tickets'])->name('tickets_data');
Route::post('/store_ticket', [SupportTicketController::class, 'store'])->name('store_ticket');
Route::post('/send_message', [SupportTicketController::class, 'send_message'])->name('send_message');
Route::get('/get_messages', [SupportTicketController::class, 'get_messages'])->name('get_messages');
Route::post('/tickets/{id}/close', [SupportTicketController::class, 'close'])->name('close_ticket');



