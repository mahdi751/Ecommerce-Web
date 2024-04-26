<?php

use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\GithubController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SubscriberController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\CouponsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\EventController;
use \UniSharp\LaravelFilemanager\Lfm;
use App\Http\Controllers\BotManController;
use App\Http\Controllers\WishlistController;


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





Route::match(['get', 'post'], 'botman', [BotManController::class, 'handle']);

Auth::routes(['register' => false]);

Route::get('user/login', [BuyerController::class, 'login'])->name('login.form');

// Route::get('/home', [FrontendController::class, 'index']);
Route::get('user/logout', [\App\Http\Controllers\SellerController::class, 'logout'])->name('user.logout');

//Route::get('SellerLogin', [SellerController::class, 'login'])->name('seller.login');



//Rates
Route::get("rates", [CurrencyController::class,"getRates"])->name("rates");
Route::get("rate/{cur}", [CurrencyController::class,"getRate"])->name("rate");
Route::get("rate/{cur}/{amount}", [CurrencyController::class,"getAmountConversted"])->name("convert");


//Currency
Route::post('/update-currency', [BuyerController::class, 'updateCurrency'])->name('updateCurrency');

Route::group(['prefix' => '/seller', 'middleware' => ['auth', 'seller']], function () {

    Route::get('/', [\App\Http\Controllers\SellerController::class, 'index'])->name('seller');

    Route::get('/file-manager', function () {
        return view('Sellers.layouts.file-manager');
    })->name('file-manager');

    Route::get('/store', [\App\Http\Controllers\StoreController::class, 'index'])->name('store.index');
    Route::resource('/store', '\App\Http\Controllers\StoreController');
    Route::post('/events/{id}/end', '\App\Http\Controllers\EventController@endEvent')->name('end.event');

    Route::resource('/event', '\App\Http\Controllers\EventController');
    Route::resource('/category', '\App\Http\Controllers\CategoryController');
    Route::post('/category/{id}/child', '\App\Http\Controllers\CategoryController@getChildByParent');

    Route::resource('/product', '\App\Http\Controllers\ProductController');

    Route::resource('/message', '\App\Http\Controllers\MessagesController');
    Route::get('/message/five', [\App\Http\Controllers\MessagesController::class, 'messageFive'])->name('messages.five');

    Route::resource('/order', '\App\Http\Controllers\OrderController');

    Route::resource('/shipping', '\App\Http\Controllers\ShippingController');

    Route::resource('/coupon', '\App\Http\Controllers\CouponsController');

    Route::get('/notification/{id}', [\App\Http\Controllers\NotificationController::class, 'show'])->name('seller.notification');
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('all.notification');
    Route::delete('/notification/{id}', [\App\Http\Controllers\NotificationController::class, 'delete'])->name('notification.delete');

    Route::get('/profile', [\App\Http\Controllers\SellerController::class, 'profile'])->name('seller-profile');
    Route::post('/profile/{id}', [\App\Http\Controllers\SellerController::class, 'profileUpdate'])->name('profile-update');

    Route::get('change-password', [\App\Http\Controllers\SellerController::class, 'changePassword'])->name('change.password.form');
    Route::post('change-password', [\App\Http\Controllers\SellerController::class, 'changPasswordRequest'])->name('change.password');
});

Route::group(['prefix' => '/user', 'middleware' => ['user']], function () {
    Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('user');
});

Route::get('/income', [OrderController::class, 'incomeChart'])->name('product.order.income');


Auth::routes([
    'verify' => true
]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// ->middleware("verified");

Route::get('/home/events', [App\Http\Controllers\EventController::class, 'buyerIndex']);
Route::get('/home/events/{event_id}/products', '\App\Http\Controllers\ProductController@showProductsForEvent')->name('products.show');

Route::post('/bids', '\App\Http\Controllers\BidController@store')->name('bids.store');
Route::put('/bids/{id}', '\App\Http\Controllers\BidController@update')->name('bids.update');
Route::delete('/bids/{id}', '\App\Http\Controllers\BidController@destroy')->name('bids.destroy');

Route::get('/home/{store_id}', [App\Http\Controllers\BuyerController::class, 'home'])->name('homestore');
Route::match(['get', 'post'], '/product/search', [BuyerController::class, 'productSearch'])->name('product.search');




Route::get('/about-us', [BuyerController::class, 'aboutUs'])->name('about-us');
    Route::get('/contact', [BuyerController::class, 'contact'])->name('contact');
    Route::post('/contact/message', [BuyerController::class, 'store'])->name('contact.store');
    Route::get('product-detail/{slug}', [BuyerController::class, 'productDetail'])->name('product-detail');
    Route::post('/product/search', [BuyerController::class, 'productSearch'])->name('product.search');
    Route::get('/product-cat/{slug}', [BuyerController::class, 'productCat'])->name('product-cat');
    Route::get('/product-sub-cat/{slug}/{sub_slug}', [BuyerController::class, 'productSubCat'])->name('product-sub-cat');
    Route::get('/product-grids', [BuyerController::class, 'productGrids'])->name('product-grids');
    Route::get('/product-lists', [BuyerController::class, 'productLists'])->name('product-lists');
    Route::match(['get', 'post'], '/filter', [BuyerController::class, 'productFilter'])->name('shop.filter');
    Route::get('/wishlist', function () {
        return view('Buyers.pages.wishlist');
    })->name('wishlist');
    Route::get('/wishlist/{slug}', [WishlistController::class, 'wishlist'])->name('add-to-wishlist');
    Route::get('wishlist-delete/{id}', [WishlistController::class, 'wishlistDelete'])->name('wishlist-delete');


//Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () { '\vendor\UniSharp\LaravelFilemanager\Lfm::routes()'; });


Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    Lfm::routes();
});


Route::group(['prefix' => '/admin', 'middleware' => ['auth', 'admin']], function () {

    Route::get('/', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin');
    Route::get('/profile', [\App\Http\Controllers\AdminController::class, 'profile'])->name('admin-profile');
    Route::post('/profile/{id}', [\App\Http\Controllers\AdminController::class, 'profileUpdate'])->name('profile-update');

    Route::get('/stores', [\App\Http\Controllers\StoreController::class, 'stores'])->name('store.stores');
    Route::get('/stores/{id}', [\App\Http\Controllers\StoreController::class, 'editAdmin'])->name('store.editAdmin');
    Route::patch('/stores/{id}', [\App\Http\Controllers\StoreController::class, 'updateAdmin'])->name('store.updateAdmin');

    Route::resource('users', '\App\Http\Controllers\UserController');
});
// Product Review
Route::resource('/review', '\App\Http\Controllers\ProductReviewController');
Route::post('product/{slug}/review', [ProductReviewController::class, 'store'])->name('review.store');



Route::get("auth/google", [GoogleAuthController::class,"redirect"])->name("google-auth");
Route::get("auth/google/call-back", [GoogleAuthController::class,"callBack"]);
//Cart
Route::get('/add-to-cart/{slug}', [CartController::class, 'addToCart'])->name('add-to-cart');
Route::post('/add-to-cart', [CartController::class, 'singleAddToCart'])->name('single-add-to-cart');
Route::get('cart-delete/{id}', [CartController::class, 'cartDelete'])->name('cart-delete');
Route::post('cart-update', [CartController::class, 'cartUpdate'])->name('cart.update');

Route::get('/cart', [CartController::class, 'index'])->name('cart');

Route::get("auth/github", [GithubController::class,"redirect"])->name("github-auth");
Route::get("auth/github/call-back", [GithubController::class,"callBack"]);



Route::post('/SendEmails', [SubscriberController::class, 'SendEmails'])->name('SendEmails');
Route::post('/Savesubscribe', [SubscriberController::class, 'SaveSubscribe'])->name('Savesubscribe');


//Checkout
Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
Route::post('cart/order', [OrderController::class, 'store'])->name('cart.order');


//Stripe
Route::get('/success/{order_id}', [OrderController::class,'stripeSuccess'])->name('payment.success');
Route::get('/cancel/{order_id}', [OrderController::class,'stripeCancel'])->name('payment.cancel');