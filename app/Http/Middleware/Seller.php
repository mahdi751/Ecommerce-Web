<?php

namespace App\Http\Middleware;

use Closure;

class Seller
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->role === 'seller') {
            return $next($request);
        } else {
            $request->session()->flash('error', 'You do not have permission to access this page');
            return redirect()->route('home');
        }
    }

}
