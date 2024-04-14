<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;

class GoogleAuthController extends Controller
{
    public function redirect(){
        return Socialite::driver("google")->redirect();
    }


    public function callBack(){
        try{
            $google_user = Socialite::driver("google")->user();


            $user = User::where("provider_id", $google_user->getId())->first();


            if(!$user){
                $new_user = User::create([
                    'name' => $google_user->getName(),
                    'email' => $google_user->getEmail(),
                    'provider_id' => $google_user->getId(),
                    'provider' => 'google'
                ]);


                Auth::login($new_user);
                return redirect()->intended('home');

            }else {
                Auth::login($user);
                return redirect()->intended('home');
            }

        }catch(\Throwable $th){
            dd($th);
        }
    }
}
