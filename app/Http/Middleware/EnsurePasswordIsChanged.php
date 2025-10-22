<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is logged in and needs to change their password
        if (Auth::check() && Auth::user()->must_change_password) {

            // Prevent redirect loop if the user is already on the change password route
            if ($request->routeIs('profile.edit') || $request->routeIs('profile.update')) {
                return $next($request);
            }

            // Flash a message and redirect to a forced password change route
            session()->flash('warning', 'You must change your temporary password before continuing.');

            return redirect()->route('profile.edit');
        }

        return $next($request);
    }
}