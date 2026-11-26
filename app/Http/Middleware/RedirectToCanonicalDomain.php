<?php

namespace App\Http\Middleware;

use App\Support\Seo;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sends visits on any other domain pointed at this site (an old domain, www.) to the same page on APP_URL with
 * a permanent 301, which is how search engines learn the site has moved. Enable with CANONICAL_REDIRECT=true.
 */
class RedirectToCanonicalDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = Seo::host();

        if (config('app.canonical_redirect') && $host && $request->isMethodSafe() && strcasecmp($request->getHost(), $host) !== 0) {
            return redirect()->to(Seo::root() . $request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
