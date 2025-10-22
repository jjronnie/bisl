<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Business;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\ValidationException;
use App\Jobs\SendWelcomeEmailJob;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;


class GoogleLoginController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google authentication.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Handle the callback from Google authentication.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $googleId = $googleUser->getId();
            $email = $googleUser->getEmail();

            // 1. Try to find the user by Google ID (Strongest link)
            $user = User::where('google_id', $googleId)->first();

            if (!$user) {
                // 2. If not found by Google ID, try to find by Email (First-time user using Google)
                $user = User::where('email', $email)->first();
            }

            if ($user) {

                // User exists locally (either by ID or Email)

                // *** First-Time Google Login - Save the Google ID ***
                // This ensures subsequent logins use the stronger Google ID validation
                if (empty($user->google_id)) {
                    $user->google_id = $googleId;
                    $user->save();
                }

                // Log the user in
                Auth::login($user);

            } else {
                // User does NOT exist in the database, deny login
                return redirect(route('login'))->with('error', 'Your account is not registered. Please contact the administrator for assistance.');
            }


            $name = auth()->user()->name;

            return redirect()->intended(route('dashboard', absolute: false))
                ->with('show_welcome', true)
                ->with('success', "Login Successful. Welcome back $name!");

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Google Callback Error: ' . $e->getMessage());

            // Handle any errors that occur during the authentication process
            return redirect(route('login'))->withErrors(['google_error' => 'Unable to authenticate with Google. Please try again.']);
        }
    }





















}
