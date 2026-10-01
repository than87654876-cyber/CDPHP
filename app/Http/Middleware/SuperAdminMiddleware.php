<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (in_array($user->role, ['superadmin', 'admin']) 
                || (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['superadmin', 'admin']))
                || (method_exists($user, 'can') && $user->can('roles.manage'))
            ) {
                return $next($request);
            }
        }

        return redirect()->route('quanly_banlamviec')->withErrors(['role' => 'Bạn không có quyền truy cập vào chức năng này.']);
    }
}
