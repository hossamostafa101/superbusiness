<?php

// app/Http/Controllers/Auth/AdminAuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'login' => 'required|string',   // email أو username
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ]);

        $credentials = filter_var($data['login'], FILTER_VALIDATE_EMAIL)
            ? ['email' => $data['login'], 'password' => $data['password']]
            : ['username' => $data['login'], 'password' => $data['password']];

        $ok = Auth::attemptWhen($credentials, function ($user) {
            return (bool) $user->is_admin;
        }, (bool)($data['remember'] ?? false));

        if (!$ok) {
            return back()->withErrors(['login' => 'بيانات الدخول غير صحيحة.'])->onlyInput('login');
        }

        $request->session()->regenerate();
        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
