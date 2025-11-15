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
     * @return \Illuminate\Http\Response
     */
    public function redirectToMicrosoft()
    {
        return Socialite::driver('microsoft')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Handle callback from Microsoft OAuth.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleMicrosoftCallback()
    {
        try {
            $microsoftUser = Socialite::driver('microsoft')->user();

            // Validate that we have an email
            if (!$microsoftUser->getEmail()) {
                \Log::error('Microsoft OAuth Error: No email provided by Microsoft');
                return redirect()->route('login')
                    ->with('error', 'Microsoft account does not have an email address. Please contact support.');
            }

            // Get user name or fallback to email
            $name = $microsoftUser->getName()
                ?? $microsoftUser->getNickname()
                ?? $microsoftUser->getEmail();

            // Find or create user
            $user = User::updateOrCreate(
                ['email' => $microsoftUser->getEmail()],
                [
                    'name' => $name,
                    'password' => bcrypt(Str::random(24)),
                    'microsoft_id' => $microsoftUser->getId(),
                    'email_verified_at' => now(),
                ]
            );

            // Update microsoft_id if user already exists but doesn't have it
            if (!$user->microsoft_id) {
                $user->update(['microsoft_id' => $microsoftUser->getId()]);
            }

            Auth::login($user, true);

            return redirect()->intended('/home');

        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            \Log::error('Microsoft OAuth InvalidStateException: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Session expired. Please try logging in again.');
        } catch (\Exception $e) {
            \Log::error('Microsoft OAuth Error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('login')
                ->with('error', 'Unable to login using Microsoft. Please try again.');
        }
    }
}
