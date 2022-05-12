<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Models\UserSocial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    protected JWTAuth $auth;
    private $dataToSend = [];

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
        $this->middleware(['socialite.social', 'web']);
    }

    public function redirect($service)
    {
        return Socialite::driver($service)->redirect();
    }

    public function callback($service)
    {
        try {
            $serviceUser = Socialite::driver($service)->stateless()->user();
        } catch (\Exception $e) {
            return redirect(env('CLIENT_BASE_URL') . '/auth/social-callback?error=Unable to login using ' . $service . '. Please try again' . '&origin=login');
        }
        /**
         * findOrCreate
         */
        $data = $this->findOrCreate($serviceUser, $service);
        $newUser = $data["action"] === "register";
        /**
         * redirect user with jwt
         */
        return redirect(env('CLIENT_BASE_URL') . '/auth/social-callback?token=' . $data["token"] . '&origin=' . ($newUser ? 'register' : 'login'));
    }

    private function findOrCreate($data, $service){
        $this->dataToSend = [];
        try {
            DB::transaction(function () use ($data, $service) {

                if ($data->getEmail()) {
                    $email = $data->getEmail();
                } else {
                    $email = $data->getId() . '@' . $service . '.local';
                }

                //search in table users
                $user = User::with('social')->where('email', $email)->first();
                //if user exist
                if ($user) {
                    $list_of_services_associed = [];
                    foreach ($user->social as $u_s) {
                        $list_of_services_associed[] = $u_s->service;
                    }

                    if (in_array($service, $list_of_services_associed)) {
                        //check if user has password
                        $user->load('social');
                        if($user->password){
                            foreach ($user->social as $u){
                                if(!$u->has_password){
                                    UserSocial::where('id', $u->id)->update(["has_password" => 1]);
                                }
                            }
                        }
                        //redirect with token
                        $this->dataToSend["token"] = $this->auth->fromUser($user);
                        $this->dataToSend["action"] = "login";
                    } else {
                        //if user has password => don't need to set password in front else need to set password
                        if($user->password){
                            $user_social = UserSocial::create([
                                "user_id" => $user->id,
                                "social_id" => $data->getId(),
                                "service" => $service,
                                "has_password" => 1
                            ]);
                        }
                        else{
                            $user_social = UserSocial::create([
                                "user_id" => $user->id,
                                "social_id" => $data->getId(),
                                "service" => $service
                            ]);
                        }
                        /***/
                        $user->save($user_social->toArray());
                        $this->dataToSend["token"] = $this->auth->fromUser($user);
                        $this->dataToSend["action"] = "login";
                    }
                }
                //if nothing found in users table
                else {
                    $user = User::create([
                        "name" => $data->getName(),
                        "email" => $data->getEmail(),
                        "password" => "",
                        "avatar" => $data->getAvatar(),
                    ]);

                    $user_social = UserSocial::create([
                        "user_id" => $user->id,
                        "social_id" => $data->getId(),
                        "service" => $service
                    ]);

                    $user->save($user_social->toArray());
                    $this->dataToSend["token"] = $this->auth->fromUser($user);
                    $this->dataToSend["action"] = "register";
                }
            });
            return $this->dataToSend;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
