<?php

use App\Http\Controllers\Api\AddressController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ForgotController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\DebitcardController;
use App\Http\Controllers\Api\OtpVerifyController;
use App\Http\Controllers\Api\ResendOtpController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\ChangePasswordController;
use App\Http\Controllers\Api\CompanyInformationController;
use App\Http\Controllers\Api\OrderCourierController;
use App\Models\Company;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//user profile
Route::post('/signup', [RegisterController::class, 'createUser'])->name('signup');
Route::post('/login', [LoginController::class, 'userlogin'])->name('login');
Route::post('/forgot', [ForgotController::class, 'forgotPassword'])->name('forgot');
Route::post('/resendotp', [ResendOtpController::class, 'resendotp'])->name('resendotp');
Route::post('/otpverify', [OtpVerifyController::class, 'otpverify'])->name('otpverify');
Route::post('/resetPassword', [ResetPasswordController::class, 'resetpassword'])->name('resetPassword');
Route::get('/getprofile',[UserProfileController::class,'getProfile'])->name('getprofile');
Route::post('/updateprofile', [UserProfileController::class, 'updateProfile'])->name('updateprofile');
Route::post('/changePassword', [ChangePasswordController::class, 'changePassword'])->name('changePassword');
Route::post('/cardSave', [DebitcardController::class, 'userCardsave'])->name('cardSave');
Route::post('/makePrimary/{id}', [DebitcardController::class, 'cardPrimary'])->name('makePrimary');
Route::post('/delete/{id}', [DebitcardController::class, 'delete'])->name('delete');
Route::post('/addAddress', [AddressController::class, 'userAddress'])->name('addAddress');
Route::post('/updateAddress', [AddressController::class, 'updateAddress'])->name('updateAddress');
Route::post('/addressPrimary/{id}', [AddressController::class, 'cardPrimary'])->name('addressPrimary');
Route::post('/addressDelete/{id}', [AddressController::class, 'delete'])->name('addressDelete');
Route::post('/addCompany', [CompanyInformationController::class, 'createCompany'])->name('addCompany');
Route::post('/updateCompany',[CompanyInformationController::class,'updateCompany'])->name('updateCompany');

//order routes
Route::post('/getQuote',[OrderCourierController::class,'getQuotes'])->name('getQuote');
Route::get('/getBuyer',[OrderCourierController::class,'getBuyer'])->name('getBuyer');
Route::post('/saveOrder',[OrderCourierController::class,'saveOrderDetail'])->name('saveOrder');
