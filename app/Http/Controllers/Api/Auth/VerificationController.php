<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends Controller
{

    /**
     * Email verification.
     */
    public function verify(Request $request)
    {
        $user_id = $request->id;

        $user = User::findOrFail($user_id);

        $date = date("Y-m-d g:i:s");

        $user->email_verified_at = $date;
        $user->save();

        return redirect("https://voay.giprod.mg/auth/login");
    }
}
