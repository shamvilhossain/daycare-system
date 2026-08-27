<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'email'      => 'required|email|unique:users,email',
            'password'   => ['required', 'confirmed', Password::min(8)],
            'role'       => 'required|in:parent,staff', // admin never self-registers
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'phone'      => 'nullable|string',
        ]);

        $user = User::create([
            'email'    => $data['email'],
            'password' => $data['password'], // hashed automatically via model cast
            'role'     => $data['role'],
        ]);

        $user->assignRole($data['role']);

        if ($data['role'] === 'parent') {
            $user->parentProfile()->create([
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'phone'      => $data['phone'] ?? null,
            ]);
        } else {
            $user->staffProfile()->create([
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'role'       => 'teacher', // default; admin can promote later
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $throttleKey = Str::lower($request->input('email')).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Try again in {$seconds} seconds.",
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60); // 60s lockout window
            return back()->withErrors(['email' => 'These credentials do not match our records.'])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);

        

        if (! Auth::user()->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'This account has been deactivated.']);
        }

        $request->session()->regenerate();
        Auth::user()->update(['last_login' => now()]);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
