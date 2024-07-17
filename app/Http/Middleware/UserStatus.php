<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Enums\AccountStatusType;
use Symfony\Component\HttpFoundation\Response;

class UserStatus
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    if (Auth::check() && me()->status == AccountStatusType::INACTIVE->value) :
      Auth::logout();
      $request->session()->invalidate();
      $request->session()->regenerateToken();
      return redirect(route('login'))->with('error', trans('session.banned'));
    endif;

    return $next($request);
  }
}
