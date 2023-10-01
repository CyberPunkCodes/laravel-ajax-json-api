<?php

namespace CyberPunkCodes\LaravelAjaxJsonApi\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;

class AjaxOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->isXmlHttpRequest()) {
            return response(['status' => '406', 'message' => 'Not Acceptable'], 406);
        }

        return $next($request);
    }
}
