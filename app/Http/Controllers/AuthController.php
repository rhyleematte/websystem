<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email.',
            'password.required' => 'Password is required.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->route('user.dashboard');
        }

        return back()
            ->withErrors([
            'email' => 'These credentials do not match our records.',
        ])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showSignup()
    {
        return view('auth.signup');
    }

    
public function signupAjax(Request $request)
    {
        $data = $request->validate([
            'fname' => ['required', 'min:2'],
            'mname' => ['nullable', 'min:2'],
            'lname' => ['required', 'min:2'],
            'gender' => ['required'],
            'bday' => ['required', 'date'],
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'min:3', 'max:20', 'unique:users,username'],
            'password' => ['required', 'min:6', 'confirmed'],
        ], [

            // First name
            'fname.required' => 'First name is required.',
            'fname.min' => 'First name must be at least 2 characters.',

            // Middle name
            'mname.min' => 'Middle name must be at least 2 characters.',

            // Last name
            'lname.required' => 'Last name is required.',
            'lname.min' => 'Last name must be at least 2 characters.',

            // Gender
            'gender.required' => 'Please select your gender.',

            // Birthday
            'bday.required' => 'Birthday is required.',
            'bday.date' => 'Invalid birth date.',

            // Email
            'email.required' => 'Email is required.',
            'email.email' => 'Enter a valid email address.',
            'email.unique' => 'This email is already registered.',

            // Username
            'username.required' => 'Username is required.',
            'username.min' => 'Username must be at least 3 characters.',
            'username.max' => 'Username cannot exceed 20 characters.',
            'username.unique' => 'Username already taken.',

            // Password
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'username' => $data['username'],
            'fname' => $data['fname'],
            'mname' => $data['mname'] ?? null,
            'lname' => $data['lname'],
            'gender' => $data['gender'],
            'bday' => $data['bday'],
            'role' => 'user',
            'doctor_status' => 'none',
            'profile_photo' => 'profiles/default.png',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Account created successfully!',
            'redirect' => route('login'),
            'user_id' => $user->id,
        ]);
    }

}

class ProfileController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('profile.show', compact('user'));
    }
}