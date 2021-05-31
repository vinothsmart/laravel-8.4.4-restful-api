<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TransfromInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $transfer)
    {
        $transformedInput = [];

        // adding condition transform value to  Original value
        // For example title = name
        foreach ($request->request->all() as $input => $value) {
            $transformedInput[$transformer::originalAttribute($input)] = $value;
        }

        // Then going to replace the value
        $request->replace($transformedInput);
        
        return $next($request);
    }
}
