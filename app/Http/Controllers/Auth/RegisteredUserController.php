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
        \Log::info('Registration attempt', $request->all());

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_\-\.]+$/', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Generate a fake email since the database still requires it
        $fakeEmail = $request->name . '@example.com';
        
        // Check if the fake email already exists
        if (User::where('email', $fakeEmail)->exists()) {
            $fakeEmail = $request->name . '_' . uniqid() . '@example.com';
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->name,
            'email' => $fakeEmail, // Add fake email to satisfy database constraint
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
