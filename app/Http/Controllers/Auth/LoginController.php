<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        $loginValue = request('username');
        
        $this->username = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'nomor_induk';

        request()->merge([$this->username=> $loginValue]);
        return property_exists($this, 'username') ? $this->username : 'email';
    }

    /**
     * Create authenticated
     *
     * 
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->hasRole('super-admin')) {
            return redirect()->route('dashboard');
        } 
        elseif ($user->hasRole('bagian-akademik')) {
            return redirect()->route('dashboard');
        }
        elseif ($user->hasRole('admin-jurusan')) {
            return redirect()->route('dashboard');
        }
        elseif ($user->hasRole('koor-pkl')) {
            return redirect()->route('dashboard');
        }
        
        return redirect()->route('home');
        
    }
}
