<?php

namespace App\Http\Middleware;

use Closure;
use App\Trait\ResponseApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserRole
{
    /**
     * -------TRAIT---------
     * Mandatory:
     * 1. ResponseApi = Response api should use ResponseApi trait template
     */
    use ResponseApi;

    /**
     * Check Role User
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param mixed $isRole
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $isRole): Response
    {
        $user = Auth::user();

        $roles = explode(',', $isRole);

        if (! in_array($user->role, $roles)) {
            return $this->returnResponseApi(false, 'Forbidden', null, 403);
        }

        return $next($request);
    }
}
