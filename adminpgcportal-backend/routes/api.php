<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;


use App\Http\Controllers\ReportsController;

// Peso Job Portal
use App\Http\Controllers\EmployersController;
use App\Http\Controllers\JobSeekersController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\JobMatchingController;
use App\Http\Controllers\NotificationController;

//PGC Portal
use App\Http\Controllers\DashController;
use App\Http\Controllers\PGCEmployeesController;
use App\Http\Controllers\PGCDirectoriesController;
use App\Http\Controllers\PGCZoomController;
use App\Http\Controllers\PGCRepairsController;
use App\Http\Controllers\PGCCertController;
use App\Http\Controllers\PGCCertPostController;
use App\Http\Controllers\PGCCertWasteController;
use App\Http\Controllers\ExportToPDFController;
use App\Http\Controllers\PGCNotificationsController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
});


Route::group(['prefix' => 'auth', 'middleware' => 'auth:sanctum'], function () {
    Route::post('me', [AuthController::class, 'me']);
    Route::post('changePassword', [AuthController::class, 'changePassword']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::group(['prefix' => 'Reports', 'middleware' => 'auth:sanctum'], function () {
    Route::get('fetchMeCost', [ReportsController::class, 'fetchMeCost']);
    Route::get('fetchOpCost', [ReportsController::class, 'fetchOpCost']);
    Route::get('fetchClass', [ReportsController::class, 'fetchClass']);
    Route::get('fetchMeCostIndi', [ReportsController::class, 'fetchMeCostIndi']);
    Route::get('fetchOpCostIndi', [ReportsController::class, 'fetchOpCostIndi']);
});

Route::group(['prefix' => 'Reports'], function () {
    Route::get('fetchOpCost2', [ReportsController::class, 'fetchOpCost2']);
});


//PGC Portal

Route::group(['prefix' => 'Dash', 'middleware' => 'auth:sanctum'], function () {
    Route::get('fetchPostInspection', [DashController::class, 'fetchPostInspection']);
    Route::get('fetchWasteCerticate', [DashController::class, 'fetchWasteCerticate']);
    Route::get('fetchNewZoomRequest', [DashController::class, 'fetchNewZoomRequest']);
    Route::get('fetchUpcomingZoom', [DashController::class, 'fetchUpcomingZoom']);
    Route::get('fetchForApproval', [DashController::class, 'fetchForApproval']);
    Route::get('fetchForReceiving', [DashController::class, 'fetchForReceiving']);
    Route::get('fetchOngoing', [DashController::class, 'fetchOngoing']);
    Route::get('fetchRepaired', [DashController::class, 'fetchRepaired']);
    Route::get('fetchNotRepaired', [DashController::class, 'fetchNotRepaired']);
});

Route::group(['prefix' => 'Dashboard', 'middleware' => 'auth:sanctum'], function () {
    Route::get('fetchPhotos', [DashboardController::class, 'fetchPhotos']);
});

Route::group(['prefix' => 'PGCEmployees', 'middleware' => 'auth:sanctum'], function () {
    Route::get('fetchEmployees', [PGCEmployeesController::class, 'fetchEmployees']);
});

Route::group(['prefix' => 'PGCDirectories', 'middleware' => 'auth:sanctum'], function () {
    Route::get('fetch', [PGCDirectoriesController::class, 'fetch']);
    Route::post('updateDirectory', [PGCDirectoriesController::class, 'updateDirectory']);
    Route::post('DeleteReq', [PGCDirectoriesController::class, 'DeleteReq']);
});

Route::group(['prefix' => 'PGCZoom', 'middleware' => 'auth:sanctum'], function () {
    Route::get('fetchRequest', [PGCZoomController::class, 'fetchRequest']);
    Route::get('fetchUpcoming', [PGCZoomController::class, 'fetchUpcoming']);
    Route::get('fetchPrev', [PGCZoomController::class, 'fetchPrev']);

    Route::post('setZoomLink', [PGCZoomController::class, 'setZoomLink']);
});

Route::group(['prefix' => 'PGCRepairs', 'middleware' => 'auth:sanctum'], function () {
    Route::get('fetchRequest', [PGCRepairsController::class, 'fetchRequest']);
    Route::get('fetchForReceiving', [PGCRepairsController::class, 'fetchForReceiving']);
    Route::get('fetchCurrent', [PGCRepairsController::class, 'fetchCurrent']);
    Route::get('fetchPrev', [PGCRepairsController::class, 'fetchPrev']);
    Route::get('fetchTech', [PGCRepairsController::class, 'fetchTech']);
    Route::get('fetchUsers', [PGCRepairsController::class, 'fetchUsers']);
    Route::get('fetchActionsTaken', [PGCRepairsController::class, 'fetchActionsTaken']);
    Route::get('fetchStatus', [PGCRepairsController::class, 'fetchStatus']);
    Route::get('updateRequest', [PGCRepairsController::class, 'updateRequest']);

    Route::post('ApproveReq', [PGCRepairsController::class, 'ApproveReq']);
    Route::post('ReceiveReq', [PGCRepairsController::class, 'ReceiveReq']);
    Route::post('DeleteReq', [PGCRepairsController::class, 'DeleteReq']);
    Route::post('AddActions', [PGCRepairsController::class, 'AddActions']);
    Route::post('UpdateStatusNotRepaired', [PGCRepairsController::class, 'UpdateStatusNotRepaired']);
    Route::post('UpdateStatusRepaired', [PGCRepairsController::class, 'UpdateStatusRepaired']);
});

Route::group(['prefix' => 'PGCCert', 'middleware' => 'auth:sanctum'], function () {
    Route::get('fetchPre', [PGCCertController::class, 'fetchPre']);
    Route::get('fetchRequest', [PGCCertController::class, 'fetchRequest']);
    Route::get('generateReferenceCode', [PGCCertController::class, 'generateReferenceCode']);

    Route::post('addPRE', [PGCCertController::class, 'addPRE']);
    Route::post('DeleteReq', [PGCCertController::class, 'DeleteReq']);
});

Route::group(['prefix' => 'PGCCertPost', 'middleware' => 'auth:sanctum'], function () {
    Route::get('fetchPost', [PGCCertPostController::class, 'fetchPost']);
    Route::get('fetchRequest', [PGCCertPostController::class, 'fetchRequest']);
    Route::get('generateReferenceCode', [PGCCertPostController::class, 'generateReferenceCode']);

    Route::post('addPRE', [PGCCertPostController::class, 'addPRE']);
    Route::post('DeleteReq', [PGCCertPostController::class, 'DeleteReq']);
});

Route::group(['prefix' => 'PGCCertWaste', 'middleware' => 'auth:sanctum'], function () {
    Route::get('fetchWaste', [PGCCertWasteController::class, 'fetchWaste']);
    Route::get('fetchRequest', [PGCCertWasteController::class, 'fetchRequest']);
    Route::get('generateReferenceCode', [PGCCertWasteController::class, 'generateReferenceCode']);

    Route::post('addPRE', [PGCCertWasteController::class, 'addPRE']);
    Route::post('DeleteReq', [PGCCertWasteController::class, 'DeleteReq']);
});

Route::group(['prefix' => 'ExportToPDF', 'middleware' => 'auth:sanctum'], function () {
    Route::get('fetchRepairs', [ExportToPDFController::class, 'fetchRepairs']);
    Route::get('fetchZoom', [ExportToPDFController::class, 'fetchZoom']);
});

Route::group(['prefix' => 'PGCNotifications'], function () {
    Route::get('triggerPostInspection', [PGCNotificationsController::class, 'triggerPostInspection']);
    Route::get('triggerWasteCertificate', [PGCNotificationsController::class, 'triggerWasteCertificate']);
    Route::get('triggerZoomPending', [PGCNotificationsController::class, 'triggerZoomPending']);
    Route::get('triggerZoomUpcoming', [PGCNotificationsController::class, 'triggerZoomUpcoming']);
    Route::get('triggerZoomPrev', [PGCNotificationsController::class, 'triggerZoomPrev']);
    Route::get('triggerPendingRepairs', [PGCNotificationsController::class, 'triggerPendingRepairs']);
    Route::get('triggerForReceivingRepairs', [PGCNotificationsController::class, 'triggerForReceivingRepairs']);
    Route::get('triggerCurrentRepairs', [PGCNotificationsController::class, 'triggerCurrentRepairs']);
});


// http://localhost:8000/api/PGCNotifications/triggerPendingRepairs
