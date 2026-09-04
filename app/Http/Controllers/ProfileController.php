<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function editPassword(): View
    {
        return view('profile.password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                'different:current_password',
                Password::min(10)->mixedCase()->numbers()->symbols(),
            ],
        ], [
            'current_password.current_password' => 'वर्तमान पासवर्ड सही नहीं है।',
            'password.different' => 'नया पासवर्ड वर्तमान पासवर्ड से अलग होना चाहिए।',
            'password.confirmed' => 'नया पासवर्ड और पुष्टि मेल नहीं खाते।',
        ]);

        Auth::logoutOtherDevices($validated['current_password']);

        $request->user()->forceFill([
            'password' => $validated['password'],
        ])->save();

        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return back()->with('status', 'पासवर्ड सफलतापूर्वक बदल दिया गया है।');
    }
}
