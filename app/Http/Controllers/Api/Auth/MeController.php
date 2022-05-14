<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSocial;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Intervention\Image\Facades\Image;
use App\Traits\MediaTrait;

class MeController extends Controller
{
    use MediaTrait;
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
    public function updateProfil(Request $request){
        if($request->user()){
            $user = $request->user();
           // dd($request->file('avatar'));
            if($request->file('avatar')) {

                $fileData = $this->uploads($request->file('avatar'), 'images/avatars/');

                $image = Image::make(public_path($fileData["filePath"]));

                $image->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                });

                $image->save();

              //  dd($fileData['filePath']);

                $data['avatar'] = $fileData['filePath'];
            }

            // dd($request->file('avatar'));

            $user->update([
                "name" => $request->input('name'),
                "email" => $request->input('email'),
                "avatar" => $data['avatar'] ?? ""
            ]);
            return response()->json([
                'data' => $user
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
}
