<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    /**
     * Idle timeout in minutes. Matches SESSION_LIFETIME in .env.
     */
    protected int $timeout = 120;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = $request->session()->get('last_activity');
            $now          = time();

            if ($lastActivity && ($now - $lastActivity) > ($this->timeout * 60)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // AJAX / JSON requests get a 401 response
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Session expired.'], 401);
                }

                return redirect()->route('login')
                    ->with('error', 'Sesi Anda telah berakhir karena tidak aktif. Silakan masuk kembali.');
            }

            // Refresh last activity timestamp on every request
            $request->session()->put('last_activity', $now);
        }

        return $next($request);
    }
}
