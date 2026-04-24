<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    DashboardController,
    IbuController,
    KehamilanController,
    AnakController,
    AsiController,
    TtdIbuController,
    PmbaController,
    RemajaController,
    TtdRemajaController,
    KonselingRuleController,
    PertumbuhanAnakController,
    MonitoringController,
    KonselingController
};

/*
|--------------------------------------------------------------------------
| API VERSIONING
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | 🔓 PUBLIC
    |--------------------------------------------------------------------------
    */
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register/ibu', [AuthController::class, 'registerIbu']);
    Route::post('/auth/register/kader', [AuthController::class, 'registerKader']);

    /*
    |--------------------------------------------------------------------------
    | 🔐 PROTECTED (SANCTUM)
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | AUTH
        |--------------------------------------------------------------------------
        */
        Route::prefix('auth')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);

            // ✅ TAMBAHKAN DI SINI
            Route::put('/profil/ibu', [AuthController::class, 'updateProfilIbu']);
            Route::put('/profil/kader', [AuthController::class, 'updateProfilKader']);
        });
        /*
        |--------------------------------------------------------------------------
        | 🔥 KADER AREA
        |--------------------------------------------------------------------------
        */
        Route::middleware('role:kader')->prefix('kader')->group(function () {

            Route::get('/dashboard', [DashboardController::class, 'index']);

            Route::apiResource('/ibu', IbuController::class);
            Route::apiResource('/kehamilan', KehamilanController::class);
            Route::apiResource('/anak', AnakController::class);
            Route::apiResource('/remaja', RemajaController::class);

            // ASI
            Route::apiResource('/asi', AsiController::class);
            Route::post('/asi/preview', [AsiController::class, 'preview']);

            // PMBA
            Route::apiResource('/pmba', PmbaController::class);
            Route::post('/pmba/preview', [PmbaController::class, 'preview']);

            // TTD
            Route::apiResource('/ttd-ibu', TtdIbuController::class);
            Route::apiResource('/ttd-remaja', TtdRemajaController::class);
            Route::post('/ttd-remaja/preview', [TtdRemajaController::class, 'preview']);

            // PERTUMBUHAN
            Route::apiResource('/pertumbuhan', PertumbuhanAnakController::class);
            Route::get('/pertumbuhan/grafik/{anak_id}', [PertumbuhanAnakController::class, 'grafik']);

            // MONITORING
            Route::prefix('monitoring')->group(function () {
                Route::get('/', [MonitoringController::class, 'index']);
                Route::post('/', [MonitoringController::class, 'store']);
                Route::get('/{id}', [MonitoringController::class, 'show']);
                Route::put('/{id}', [MonitoringController::class, 'update']);
                Route::delete('/{id}', [MonitoringController::class, 'destroy']);
                Route::post('/preview', [MonitoringController::class, 'preview']);
                Route::post('/run-rule', [KonselingRuleController::class, 'runRule']);
                Route::get('/last/{id}', [MonitoringController::class, 'last']);
            });

            // KONSELING
            Route::apiResource('/konseling', KonselingController::class);

            // RULE ENGINE
            Route::prefix('rule')->group(function () {
                Route::apiResource('/', KonselingRuleController::class);
            });
        });

        /*
        |--------------------------------------------------------------------------
        | 🔥 IBU AREA
        |--------------------------------------------------------------------------
        */
        Route::middleware('role:ibu')->prefix('ibu')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Api\Ibu\DashboardController::class, 'index']);

            Route::apiResource('/anak', \App\Http\Controllers\Api\Ibu\AnakController::class);
            Route::apiResource('/kehamilan', \App\Http\Controllers\Api\Ibu\KehamilanController::class);

            // ASI
            Route::apiResource('/asi', \App\Http\Controllers\Api\Ibu\AsiController::class);
            Route::post('/asi/preview', [\App\Http\Controllers\Api\Ibu\AsiController::class, 'preview']);

            // PMBA
            Route::apiResource('/pmba', \App\Http\Controllers\Api\Ibu\PmbaController::class);
            Route::post('/pmba/preview', [\App\Http\Controllers\Api\Ibu\PmbaController::class, 'preview']);

            // PERTUMBUHAN
            Route::apiResource('/pertumbuhan', \App\Http\Controllers\Api\Ibu\PertumbuhanAnakController::class);
            Route::get('/pertumbuhan/grafik/{anak_id}', [\App\Http\Controllers\Api\Ibu\PertumbuhanAnakController::class, 'grafik']);

            // MONITORING
            Route::prefix('monitoring')->group(function () {
                Route::get('/', [\App\Http\Controllers\Api\Ibu\MonitoringController::class, 'index']);
                Route::post('/', [\App\Http\Controllers\Api\Ibu\MonitoringController::class, 'store']);
                Route::get('/{id}', [\App\Http\Controllers\Api\Ibu\MonitoringController::class, 'show']);
                Route::post('/preview', [\App\Http\Controllers\Api\Ibu\MonitoringController::class, 'preview']);
            });
        });
    });
});
