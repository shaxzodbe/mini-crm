<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function showLoginForm()
    {
        if (auth()->check() && auth()->user()->hasRole('manager')) {
            return redirect()->route('manager.dashboard');
        }
        return view('auth.login');
    }


    protected $redirectAfterLogout = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'showLoginForm']);
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated(Request $request, $user): RedirectResponse|null
    {
        if ($user->hasRole('manager')) {
            return redirect()->route('manager.dashboard');
        }

        return null;
    }

    protected function loggedOut(Request $request)
    {
        return redirect($this->redirectAfterLogout);
    }
}
