<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;

class ActivityLogger
{
    public function handle(Request $request, Closure $next, string $module = 'general')
    {
        $response = $next($request);

        if (auth()->check() && $request->isMethod('post') || $request->isMethod('patch') || $request->isMethod('put') || $request->isMethod('delete')) {
            ActivityLog::record(
                strtoupper($request->method()),
                $module,
                'Akses ' . $request->path() . ' via ' . $request->method()
            );
        }

        return $response;
    }
}