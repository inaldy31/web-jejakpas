<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrustHosts extends Middleware
{
    public function handle(Request $request, $next): Response
    {
        return parent::handle($request, function (Request $request) use ($next) {
            $request->getHost();

            return $next($request);
        });
    }
}
