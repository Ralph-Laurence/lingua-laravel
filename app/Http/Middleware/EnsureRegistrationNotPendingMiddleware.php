<?php

namespace App\Http\Middleware;

use App\Models\FieldNames\ProfileFields;
use App\Models\PendingRegistration;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegistrationNotPendingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && PendingRegistration::where(ProfileFields::UserId, Auth::user()->id)->exists())
        {
            session()->flash('msgIsPending', 'Your profile is still pending verification. We will notify you via email once your registration has been verified or approved by our moderators.');
            return redirect('/');
        }

        return $next($request);
    }
}
