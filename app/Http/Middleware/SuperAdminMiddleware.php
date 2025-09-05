<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\UserType;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Authentication required.');
        }

        $superAdminType = UserType::where('name', 'Super Admin')->first();
        
        if (!$superAdminType || $user->userType?->id !== $superAdminType->id) {
            abort(403, 'Super Admin access required.');
        }

        // Add super admin capabilities to the request
        $request->merge(['is_super_admin' => true]);
        
        return $next($request);
    }
}
