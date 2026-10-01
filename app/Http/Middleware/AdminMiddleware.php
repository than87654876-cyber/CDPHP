<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
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
            if (in_array($user->role, ['superadmin', 'admin', 'staff', 'kitchen', 'shipper']) 
                || (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['superadmin', 'admin', 'staff', 'kitchen', 'shipper']))
                || (method_exists($user, 'getAllPermissions') && $user->getAllPermissions()->isNotEmpty())
            ) {
                return $next($request);
            }
        }

        return redirect()->route('dangnhap')->withErrors(['email' => 'Vui lòng đăng nhập bằng tài khoản Quản lý/Nhân viên.']);
    }
}
