<?php

use App\Mail\NewsLetterController as MailNewsLetterController;
use App\Models\NewsLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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

Route::post('newsletter', function(Request $request){
    $request->validate([
        'email' => 'required|email',
    ]);

    if(!$request->input('email')){
        return response()->json(["errors" => "Veuillez remplir "]);
    }

    $user = NewsLetter::create($request->all());

    Mail::to($user->email)->send(new MailNewsLetterController($user));

    return response()->json([
        'message' => 'Thanks for subscribing to our newsletter!'
    ]);
});

require_once 'api/auth/auth.php';
require_once 'api/article/article.php';
