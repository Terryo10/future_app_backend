<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BotManController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post('/send_message', [ChatController::class, 'sendMessage'])->middleware('auth:sanctum');
Route::get('/my_chats', [ChatController::class, 'getChats'])->middleware('auth:sanctum');
Route::get('/search_users', [ChatController::class, 'searchUsers'])->middleware('auth:sanctum');
Route::get('/device_tokens',[ChatController::class, 'tokens'])->middleware('auth:sanctum');
Route::post('/init_chat',[ChatController::class,'chatInit'])->middleware('auth:sanctum');
Route::get('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/article_categories',[ArticleController::class , 'getArticles'])->middleware('auth:sanctum');
Route::post('comment_article', [ArticleController::class, 'commentArticle'])->middleware('auth:sanctum');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class,'register']);

//Route::match(['get', 'post'], 'botman', [BotManController::class, 'handle']);
