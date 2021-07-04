<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SwipeController;
use App\Http\Controllers\MatchController;

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

Route::get('/tv/popular', function(Request $req) {
	$data = \Cache::get('tv/popular');
	(new \Symfony\Component\Console\Output\ConsoleOutput())->writeln("Trying from Cache...");
	if ($data != null) return $data;
	(new \Symfony\Component\Console\Output\ConsoleOutput())->writeln("Nothing saved on Cache. Getting data...");

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, 'https://api.themoviedb.org/3/tv/popular?api_key=c20660fd566699db614d2e86a63d4e81');
	$data = curl_exec($ch);

	\Cache::put('tv/popular', $data, now()->addMinutes(60));
	return $data;
});

Route::get('/movie/popular', function(Request $req) {
	$data = \Cache::get('movie/popular');
	(new \Symfony\Component\Console\Output\ConsoleOutput())->writeln("Trying from Cache...");
	if ($data != null) return $data;
	(new \Symfony\Component\Console\Output\ConsoleOutput())->writeln("Nothing saved on Cache. Getting data...");

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, 'https://api.themoviedb.org/3/movie/popular?api_key=c20660fd566699db614d2e86a63d4e81');
	$data = curl_exec($ch);

	\Cache::put('movie/popular', $data, now()->addMinutes(60));
	return $data;
});

Route::get('/tv/popular/{page}', function(Request $req, $page) {
	$data = \Cache::get('tv/popular' . $page);
	(new \Symfony\Component\Console\Output\ConsoleOutput())->writeln("Trying from Cache...");
	if ($data != null) return $data;
	(new \Symfony\Component\Console\Output\ConsoleOutput())->writeln("Nothing saved on Cache. Getting data...");

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, 'https://api.themoviedb.org/3/tv/popular?api_key=c20660fd566699db614d2e86a63d4e81&page=' . $page);
	$data = curl_exec($ch);

	\Cache::put('tv/popular' . $page, $data, now()->addMinutes(60));
	return $data;
});

Route::get('/movie/popular/{page}', function(Request $req, $page) {
	$data = \Cache::get('movie/popular' . $page);
	(new \Symfony\Component\Console\Output\ConsoleOutput())->writeln("Trying from Cache...");
	if ($data != null) return $data;
	(new \Symfony\Component\Console\Output\ConsoleOutput())->writeln("Nothing saved on Cache. Getting data...");

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, 'https://api.themoviedb.org/3/movie/popular?api_key=c20660fd566699db614d2e86a63d4e81&page=' . $page);
	$data = curl_exec($ch);

	\Cache::put('movie/popular' . $page, $data, now()->addMinutes(60));
	return $data;
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);    
});

Route::middleware('jwt.verify')->get('/test', function(Request $request) {
	$uid = $request->user()->id;
	return \App\Models\Matches::where('user1', $uid)->orWhere('user2', $uid)->get()->first()->otherUser;
});

// ALL NEED JWT.VERIFY

Route::group(['jwt.verify'], function ($router) {
	Route::post('/swipe/store', [SwipeController::class, 'store']);
	Route::get('/swipe', [SwipeController::class, 'index']);
	Route::get('/matches', [MatchController::class, 'index']);
});
