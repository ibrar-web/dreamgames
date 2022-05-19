<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserGamesController;
use App\Http\Controllers\UserHomeController;
use Illuminate\Support\Facades\Log;
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
Route::get('/', function(){
    return view('test');
});
Route::get('/onetwo', function(){
    return view('test');
});
Route::get('/game/{gameid}/{userid}/{token}/{username}', [UserGamesController::class,'game']);
// Route::middleware('auth:web')->group(function () {
    Route::post('/user_actions/gamePlay.php', [UserGamesController::class,'findgame']);
    Route::post('/detail', [UserHomeController::class, 'detail']);

    Route::get('/findgames/{gameid}/{userid}/{token}',[UserGamesController::class,'findgame']);
    Route::get('/getinfo', [UserHomeController::class, 'getinfo']);
    Route::post('/update1', [UserHomeController::class, 'update1']);
    Route::post('/update2', [UserHomeController::class, 'update2']);
    Route::post('/update3', [UserHomeController::class, 'update3']);
    Route::post('/update4', [UserHomeController::class, 'update4']);
    Route::post('/update5', [UserHomeController::class, 'update5']);
    
    Route::get('/getusercreditgame6', [UserHomeController::class, 'getusercreditgame6']);
    Route::post('/update6', [UserHomeController::class, 'update6']);
    
    Route::post('/update8', [UserHomeController::class, 'update8']);
    Route::post('/update9', [UserHomeController::class, 'update9']);
    
    Route::post('/update10', [UserHomeController::class, 'update10']);
    
    Route::post('/update12', [UserHomeController::class, 'update12']);
    Route::post('/update13', [UserHomeController::class, 'update13']);
    Route::post('/update14', [UserHomeController::class, 'update14']);
    Route::post('/update15', [UserHomeController::class, 'update15']);
    Route::post('/update16', [UserHomeController::class, 'update16']);
    Route::post('/update17', [UserHomeController::class, 'update17']);
    Route::post('/update18', [UserHomeController::class, 'update18']);
    Route::post('/update19', [UserHomeController::class, 'update19']);
    Route::post('/update20', [UserHomeController::class, 'update20']);
    Route::post('/update21', [UserHomeController::class, 'update21']);
    Route::post('/update22', [UserHomeController::class, 'update22']);
    Route::post('/update23', [UserHomeController::class, 'update23']);
    Route::post('/update24', [UserHomeController::class, 'update24']);
    Route::post('/update25', [UserHomeController::class, 'update25']);
    Route::post('/update26', [UserHomeController::class, 'update26']);
    Route::post('/update27', [UserHomeController::class, 'update27']);
    Route::post('/update28', [UserHomeController::class, 'update28']);
    Route::post('/update29', [UserHomeController::class, 'update29']);
    Route::post('/update30', [UserHomeController::class, 'update30']);
    Route::post('/update31', [UserHomeController::class, 'update31']);
    Route::post('/update32', [UserHomeController::class, 'update32']);
    Route::post('/update33', [UserHomeController::class, 'update33']);
    Route::post('/update34', [UserHomeController::class, 'update34']);
    Route::post('/update35', [UserHomeController::class, 'update35']);
    Route::post('/update36', [UserHomeController::class, 'update36']);
    Route::post('/update37', [UserHomeController::class, 'update37']);
    Route::post('/update38', [UserHomeController::class, 'update38']);
    Route::post('/update39', [UserHomeController::class, 'update39']);
    Route::post('/update40', [UserHomeController::class, 'update40']);
    Route::post('/update41', [UserHomeController::class, 'update41']);
    Route::post('/update42', [UserHomeController::class, 'update42']);
    Route::post('/update43', [UserHomeController::class, 'update43']);
    Route::post('/update44', [UserHomeController::class, 'update44']);
    Route::post('/update45', [UserHomeController::class, 'update45']);
    Route::post('/update46', [UserHomeController::class, 'update46']);
    Route::post('/update47', [UserHomeController::class, 'update47']);
    Route::post('/update48', [UserHomeController::class, 'update48']);
    Route::post('/update49', [UserHomeController::class, 'update49']);
    Route::post('/update50', [UserHomeController::class, 'update50']);
     Route::post('/fish', [UserHomeController::class, 'fish']);
// });
//require __DIR__.'/auth.php';
