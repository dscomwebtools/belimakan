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
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $sessionId = Session::getId();
        $now = Carbon::now();

        $visitor = Visitor::where('session_id', $sessionId)->first();

        if (!$visitor) {
            Visitor::create([
                'session_id' => $sessionId,
                'count' => 1,
                'last_increment_at' => $now,
            ]);
        } else {
            $lastVisit = $visitor->last_increment_at;
            if (!$lastVisit || $now->diffInHours($lastVisit) >= 2) {
                $visitor->increment('count');
                $visitor->update(['last_increment_at' => $now]);
            }
        }

        return $next($request);
    }
}
