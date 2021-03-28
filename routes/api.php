<?php

use App\Http\Controllers\DeepDiveDashController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyzeController;
use App\Http\Controllers\GatekeeperProductsController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/popularProducts/{filter}/{user_id}', [AnalyzeController::class, 'popularProducts']);
Route::get('/deepDive/{n}/{filter}/{user_id}', [DeepDiveDashController::class, 'byOrder']);
Route::get('/getSelectData', [GatekeeperProductsController::class, 'getSelectData']);
Route::post('/filterData', [GatekeeperProductsController::class, 'getFilteredData']);
