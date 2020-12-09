<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Str;

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

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $user = User::where([['matricula', preg_replace('/[^0-9]/', '', $request->matricula)], ['tipo', 'A']])->first();
        if ($user) {
            $nome = trim(mb_convert_case(explode(' ', Str::ascii($user->nome))[0], MB_CASE_LOWER));

            if ($nome == trim(mb_convert_case(Str::ascii($request->nome), MB_CASE_LOWER))) {
                if ($this->attemptLogin($request)) {
                    return $this->sendLoginResponse($request);
                }
            }
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function logout()
    {
        Auth::logout();

        return redirect('home');
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'matricula'  => 'required|string',
            'nome'       => 'required|string',
        ]);
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPath());
    }

    protected function attemptLogin(Request $request)
    {
        $user = User::where('matricula', preg_replace('/[^0-9]/', '', $request->matricula))->first();
        if ($user) {
            Auth::login($user);
        } else {
            return false;
        }
    }
}
