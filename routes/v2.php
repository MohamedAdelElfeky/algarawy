<?php

use App\Http\Controllers\Api\ChatController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\SearchUserController;

/*
|--------------------------------------------------------------------------
| V2 Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "v2" middleware group. Enjoy building your API!
|
*/


Route::middleware(['blocked'])->group(function () {
    Route::get('user/search', [SearchUserController::class, 'searchUser']);
    Route::delete('/conversations/{id}/participants', [ChatController::class, 'removeParticipantsFromConversation']);

   
});
