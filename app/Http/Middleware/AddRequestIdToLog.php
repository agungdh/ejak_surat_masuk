<?php

namespace App\Http\Middleware;

use App\AddLogRequestId;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddRequestIdToLog
{
    use AddLogRequestId;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->headers->get('X-Request-Id');

        $this->addRequestId($requestId);

        return $next($request);
    }
}
