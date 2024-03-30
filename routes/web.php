<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

// Route::get('/home', [FrontendController::class, 'index']);
Route::get('user/logout', [SellerController::class, 'logout'])->name('user.logout');

//Route::get('SellerLogin', [SellerController::class, 'login'])->name('seller.login');

Route::group(['prefix' => '/seller', 'middleware' => ['auth', 'seller']], function () {
    Route::get('/', [SellerController::class, 'index'])->name('seller');

    Route::get('/file-manager', function () {
        return view('Sellers.layouts.file-manager');
    })->name('file-manager');

    Route::resource('users', 'UserController');

    Route::resource('/category', 'CategoryController');
    Route::post('/category/{id}/child', 'CategoryController@getChildByParent');

    Route::resource('/product', 'ProductController');

    Route::resource('/message', 'MessagesController');
    Route::get('/message/five', [MessagesController::class, 'messageFive'])->name('messages.five');


    Route::resource('/order', 'OrderController');

    Route::resource('/shipping', 'ShippingController');

    Route::resource('/coupon', 'CouponsController');

    Route::get('/notification/{id}', [NotificationController::class, 'show'])->name('seller.notification');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('all.notification');
    Route::delete('/notification/{id}', [NotificationController::class, 'delete'])->name('notification.delete');

    Route::get('/profile', [SellerController::class, 'profile'])->name('seller-profile');
    Route::post('/profile/{id}', [SellerController::class, 'profileUpdate'])->name('profile-update');

    Route::get('change-password', [SellerController::class, 'changePassword'])->name('change.password.form');
    Route::post('change-password', [SellerController::class, 'changPasswordRequest'])->name('change.password');
});


Route::group(['prefix' => '/user', 'middleware' => ['user']], function () {
    Route::get('/', [HomeController::class, 'index'])->name('user');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
