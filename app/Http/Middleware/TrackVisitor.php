<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sessionId = Session::getId();
        $ip = $request->ip();
        $ua = $request->userAgent();
        $now = Carbon::now();

        $isBot = preg_match('/bot|crawl|spider|slurp|facebook|curl|wget/i', $ua);

        $visitor = Visitor::where('ip_address', $ip)->first();

        if (!$visitor) {
            Visitor::create([
                'session_id'       => $sessionId,
                'ip_address'       => $ip,
                'user_agent'       => $ua,
                'is_bot'           => $isBot,
                'count'            => 1,
                'last_increment_at' => $now,
            ]);
        } else {
            if (!$isBot) {
                $lastVisit = $visitor->last_increment_at;
                if (!$lastVisit || $now->diffInHours($lastVisit) >= 2) {
                    $visitor->increment('count');
                    $visitor->update(['last_increment_at' => $now]);
                }
            }

            $visitor->update([
                'user_agent' => $ua,
                'is_bot'     => $isBot,
            ]);
        }

        return $next($request);
    }
}
