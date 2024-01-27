<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseFormatter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if ($request->is('api/*') && $request->user()) {
            $roles = explode('|', $role);
            $isRole = false;
            foreach ($roles as $rolename) {
                if ($request->user()->role === $rolename) {
                    $isRole = true;
                }
            }
            if (!$isRole) {
                return ResponseFormatter::responseError(message: 'Anda tidak memiliki hak akses', code: 403);
            }
        }
        return $next($request);
    }
}
