<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\User;
use Illuminate\Support\Facades\Hash;

class GoogleController extends Controller
{
    //
    public function redirect($providerss)
    {
        return Socialite::driver($providerss)->redirect();
    }
 
    public function callback(Request $request, $providerss)
    {
 
        // jika user masih login lempar ke home
        if (Auth::check()) {
            return redirect('/home');
        }
        $state = $request->get('state');
        $request->session()->put('state',$state);
        try{
            $oauthUser = Socialite::driver($providerss)->user();
            $user = User::where('provider_id', $oauthUser->id)->first();
        } catch (Exception $e){
            dd($e);
        }
        if ($user) {
            Auth::login($user);
            // dd("masuk");
            // Auth::loginUsingId($user->id);
            return redirect('/home');
        } else {
            $newUser = User::create([
                'name' => $oauthUser->name,
                'email' => $oauthUser->email,
                'provider' => $providerss,
                'provider_id'=> $oauthUser->id
            ]);
            Auth::login($newUser);
            return redirect('/home');
        }
    }
}
