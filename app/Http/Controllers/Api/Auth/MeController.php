<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSocial;
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
                'data' => $request->user()->load(['social'])
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

    public function setPassword(Request $request){
        $validator = \Validator::make($request->all(),[
            "user_id" => "required",
            "password" => "required|min:6"
        ], ["min" => "Le champs :attribute doit faire au moins :min caractères"]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }

        $check_user = User::with(['social'])->where('id', $request->input('user_id'))->first();

        $user_to_edit = User::where('id', $request->input('user_id'))->first();
        $user_to_edit->update(["password" => \Hash::make($request->input('password'))]);

        if($check_user->social){
            foreach ($check_user->social as $user_social){
                if($user_social->has_password == 0){
                    UserSocial::where('id', $user_social->id)->update(["has_password" => 1]);
                }
            }
        }
        return response()->json(["message" => "L'opération a été effectué avec succès"]);
    }
}
