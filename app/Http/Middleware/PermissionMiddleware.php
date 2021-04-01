<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next, $role)
  {
    $user = Auth::user();

    if ($user->role !== $role) {
      return response('Forbidden', 403);
    }
    return $next($request);
  }
}
