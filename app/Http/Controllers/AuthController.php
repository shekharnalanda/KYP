<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'ईमेल या पासवर्ड सही नहीं है।'])->onlyInput('email');
        }

        $request->session()->regenerate();

        if (! $request->user()->isActive()) {
            Auth::logout();
            return back()->withErrors(['email' => 'यह खाता फिलहाल सक्रिय नहीं है।']);
        }

        return redirect()->intended(route('dashboard'));
    }

    public function dashboard(Request $request): RedirectResponse
    {
        return match ($request->user()->role) {
            'admin', 'master_admin' => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            default => redirect()->route('student.dashboard'),
        };
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
