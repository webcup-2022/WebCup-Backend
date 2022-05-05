<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

class MeController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    public function index(Request $request)
    {
        if($request->user()){
            return response()->json([
                'data' => $request->user()
            ]);
        }
        else{
            return response()->json([
                "errors" => [
                    "message" => "Invalid Token"
                ]
            ]);
        }

    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        if($this->auth->user()){
            auth()->logout();

            return response()->json([
                'success' => true
            ]);
        }
        else{
            return response()->json([
                "errors" => [
                    "message" => "Invalid token"
                ]
            ]);
        }

    }
}
