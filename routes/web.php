<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [ItemController::class, 'index'])->name('home');

Route::resource('items', ItemController::class);
Route::get('/items/{id}', [ItemController::class, 'show'])->name('items.show');
Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit')->middleware('auth');
Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update')->middleware('auth');



Route::resource('categories', CategoryController::class);

Route::get('/dashboard', [ItemController::class, 'index'])
->middleware(['auth', 'verified'])->name('home');

//IMPORTANT - middleware auth make 
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::post('/cart/remove', [CartController::class, 'remove']);
    Route::post('/cart/change-quantity', [CartController::class, 'changeQuantity']);



    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

    Route::get('/my-items', [ItemController::class, 'userItems'])->name('items.user');
});

//ADMIN ROUTES
Route::prefix('admin')->middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/items', [AdminController::class, 'items'])->name('admin.items');
    // ud operations needed
});

Route::get('/register', [RegisteredUserController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/seller/{user}', [ProfileController::class, 'showSellerProfile'])->name('profile.show');

Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy')->middleware('admin');


    //dogshit test
Route::get('/chat/{conversationId?}', [ChatController::class, 'showChatInterface'])
    ->name('chat.interface')
    ->middleware('auth');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/chat/start', [ChatController::class, 'startChat'])->name('chat.start');
    Route::get('/chat/conversations/{conversationId}', [ChatController::class, 'fetchMessages'])->name('chat.conversations');
    Route::post('/chat/messages', [ChatController::class, 'sendMessage'])->name('chat.messages');
});

require __DIR__.'/auth.php';
