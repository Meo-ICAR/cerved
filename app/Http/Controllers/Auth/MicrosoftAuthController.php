<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftAuthController extends Controller
{
    /**
     * Redirect the user to the Microsoft authentication page.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToMicrosoft()
    {
        return Socialite::driver('microsoft')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Obtain the user information from Microsoft.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleMicrosoftCallback()
    {
        try {
            $microsoftUser = Socialite::driver('microsoft')->user();

            // Find or create user
            $user = User::updateOrCreate(
                ['email' => $microsoftUser->getEmail()],
                [
                    'name' => $microsoftUser->getName(),
                    'password' => bcrypt(Str::random(24)),
                    'microsoft_id' => $microsoftUser->getId(),
                    'email_verified_at' => now(),
                ]
            );

            // Log the user in
            Auth::login($user, true);

            return redirect()->intended('/home');

        } catch (\Exception $e) {
            \Log::error('Microsoft OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Unable to login using Microsoft. Please try again.');
        }
    }
}
