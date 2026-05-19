<?php

// app/Http/Controllers/Auth/PanelAuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PanelAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.panel_login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ]);

        $credentials = filter_var($data['login'], FILTER_VALIDATE_EMAIL)
            ? ['email' => $data['login'], 'password' => $data['password']]
            : ['username' => $data['login'], 'password' => $data['password']];

        $ok = Auth::attemptWhen($credentials, function ($user) {
            return !$user->is_admin && $user->type === \App\Models\User::TYPE_RESTAURANT;
        }, (bool)($data['remember'] ?? false));

        if (!$ok) {
            return back()->withErrors(['login' => 'بيانات الدخول غير صحيحة.'])->onlyInput('login');
        }

        $request->session()->regenerate();
        return redirect()->route('panel.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('panel.login');
    }
}
