<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password as PasswordRule;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    
public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'name'                  => ['required','string','max:255'],
        'email'                 => ['required','string','email','max:255','unique:users'],
        'username'              => ['nullable','string','max:50','regex:/^[A-Za-z0-9_\.]+$/','unique:users,username'],
        'password'              => ['required', 'confirmed', PasswordRule::defaults()],
        'accept_gdpr'           => ['accepted'],   // إلزامي
        'accept_terms'          => ['accepted'],   // إلزامي
        'marketing_opt_in'      => ['nullable','boolean'], // اختياري
    ]);
    $username = $validated['username'] ?? $validated['name'].rand(1000,9999);
    $user = User::create([
        'name'  => $validated['name'],
        'email' => $validated['email'],
        'username' => $username,              
        'password' => Hash::make($validated['password']),
        'gdpr_accepted_at'   => now(),
        'terms_accepted_at'  => now(),
        'marketing_opt_in'   => !empty($validated['marketing_opt_in']),
    ]);

    event(new Registered($user));

    Auth::login($user);

    return redirect()->route('onboarding.create');
    // return redirect(RouteServiceProvider::HOME);
}
}
