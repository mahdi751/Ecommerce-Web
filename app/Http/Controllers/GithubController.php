<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GithubController extends Controller
{
    public function redirect(){
        return Socialite::driver("github")->redirect();
    }


    public function callBack(){
        try{
            $github_user = Socialite::driver("github")->user();


            $user = User::where("provider_id", $github_user->getId())->first();


            if(!$user){
                $new_user = User::create([
                    'name' => $github_user->getName(),
                    'email' => $github_user->getEmail(),
                    'provider_id' => $github_user->getId(),
                    'provider' => 'github'
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
