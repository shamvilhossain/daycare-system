<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Support both mobile and legacy phone parameter; normalize spaces and hyphens
        $rawMobile = $request->input('mobile', $request->input('phone'));
        if ($rawMobile !== null) {
            $cleanedMobile = preg_replace('/[\s\-]/', '', (string)$rawMobile);
            $request->merge(['mobile' => $cleanedMobile]);
        }

        $data = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'required|in:parent,staff', // admin never self-registers
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'mobile' => [
                'required',
                'regex:/^(?:\+?88|88)?01[3-9]\d{8}$/',
            ],
        ], [
            'mobile.required' => 'The mobile number is required.',
            'mobile.regex' => 'Please enter a valid Bangladeshi mobile number (e.g. 017xxxxxxxx, 018xxxxxxxx, 019xxxxxxxx).',
        ]);

        $user = DB::transaction(function () use ($data) {
            $isStaff = $data['role'] === 'staff';
            $isActive = $isStaff ? 0 : 1;

            $user = User::create([
                'email' => strtolower(trim($data['email'])),
                'password' => $data['password'],
                'role' => $data['role'],
                'is_active' => $isActive,
            ]);

            $user->assignRole($data['role']);

            if ($data['role'] === 'parent') {
                $user->parentProfile()->create([
                    'first_name' => trim($data['first_name']),
                    'last_name' => trim($data['last_name']),
                    'mobile' => $data['mobile'],
                ]);
            } else {
                $user->staffProfile()->create([
                    'first_name' => trim($data['first_name']),
                    'last_name' => trim($data['last_name']),
                    'mobile' => $data['mobile'],
                    'role' => 'teacher',
                    'is_active' => 0,
                ]);
            }

            return $user;
        });

        // Staff self-registration requires administrator activation before login
        if ($user->role === 'staff' || !$user->is_active) {
            return redirect()->route('login')->with('success', 'Registration successful! Your staff account is pending administrator approval before you can sign in.');
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
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Try again in {$seconds} seconds.",
            ]);
        }

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60); // 60s lockout window
            return back()->withErrors(['email' => 'These credentials do not match our records.'])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);

        if (!Auth::user()->is_active) {
            $isStaff = Auth::user()->role === 'staff';
            Auth::logout();
            return back()->withErrors([
                'email' => $isStaff
                    ? 'Your staff account is currently inactive or pending administrator approval.'
                    : 'This account has been deactivated.'
            ])->onlyInput('email');
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
