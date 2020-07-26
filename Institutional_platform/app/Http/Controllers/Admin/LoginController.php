<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\support\Facades\Auth;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use App\Admin;


class LoginController extends Controller
{
    //
    use ThrottlesLogins;
    // number of attempts

    public $maxAttempts = 5;
   
    // number of minute to lock login

    public $lockMinutes = 3;

   // protected function hasTooManyLoginAttempts(Request $request){
   //     $maxAttempts = 3;
    //    $lockMinutes = 3;
//
      //  return $this->limiter()->tooManyAttempts(
      //      $this->throttleKey($request), $maxAttempts, $lockMinutes
    //    );
 //   }

    public function __construct(){
        $this->middleware('guest:admins')->except('logout');
    }

    public function showLoginForm(){
        
        return view('Admin.Login');
    }

    public function authenticate(Request $request){

        $credentials = $request->only('username', 'password');

        /*
            login user
        */
        if (Auth::guard('admins')->attempt($credentials)){
            return redirect()->intended(route('dashboard'));

        }

         /* 
        using login throttle to check attempt
        */
        
        if($this->hasTooManyLoginAttempts($request)){
            //fire out lockout event
            $this->fireLockoutEvent($request);

            // send user out of lock

            //redirect the user back after lockout.
            return $this->sendLockoutResponse($request);
        }

        // keep track of user login attempts
        $this->incrementLoginAttempts($request);
        return view('Admin.Login')->withErrors(['username or password is incorrect!']);
    }

    public function logout(){
        Auth::guard('admins')->logout();
        return redirect('admin/login');
    }

    public function username(){
        return 'username';
    }
}

