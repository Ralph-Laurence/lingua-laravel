<?php

namespace App\Http\Middleware;

use App\Models\FieldNames\UserFields;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LearnerToTutorRegistrationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->{UserFields::Role} == User::ROLE_LEARNER)
        {
            return redirect()->route('become-tutor');
        }

        return $next($request);
    }
}
