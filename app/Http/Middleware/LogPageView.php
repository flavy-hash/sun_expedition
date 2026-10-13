<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use App\Support\GeoIp;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Anonymous page view statistics. No cookies; the IP address and user agent are only used, in memory,
 * to build a daily-rotating hash and look up a country code. Neither is ever stored.
 */
class LogPageView
{
    private const BOTS = '/bot|crawl|spider|slurp|preview|headless|lighthouse|pingdom|monitor|curl|wget|python|httpclient|okhttp|java\/|go-http|axios|node-fetch|scrapy|facebookexternalhit|whatsapp|embedly|feedfetcher/i';

    public function __construct(private GeoIp $geoIp) {}

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    // Runs after the response has been sent, so logging never slows a page down.
    public function terminate(Request $request, Response $response): void
    {
        try {
            if ($this->shouldLog($request, $response)) {
                PageView::create($this->record($request));
            }
        } catch (Throwable $e) {
            report($e); // Analytics must never break the site.
        }
    }

    public function shouldLog(Request $request, Response $response): bool
    {
        $userAgent = (string) $request->userAgent();

        return config('analytics.enabled')
            && $request->isMethod('GET')
            && $response->getStatusCode() === 200
            && Str::contains((string) $response->headers->get('Content-Type'), 'text/html')
            && $request->route()?->getName() !== null
            && ! $request->is(...config('analytics.excluded_paths'))
            && ! $request->user()
            && $request->header('DNT') !== '1'
            && $request->header('Sec-GPC') !== '1'
            && ! Str::contains(strtolower($request->header('Sec-Purpose', '') . ' ' . $request->header('Purpose', '')), ['prefetch', 'prerender'])
            && $userAgent !== ''
            && ! preg_match(self::BOTS, $userAgent);
    }

    private function record(Request $request): array
    {
        $routeName = $request->route()->getName();

        return [
            'visitor_hash' => self::visitorHash((string) $request->ip(), (string) $request->userAgent(), now()->toDateString()),
            'path' => Str::limit('/' . ltrim($request->path(), '/'), 490, ''),
            // Route names contain dots, so look them up in the map rather than via config('…page_types.tours.show').
            'page_type' => config('analytics.page_types', [])[$routeName] ?? $routeName,
            'tour_id' => $request->attributes->get('viewed_tour_id'),
            'device' => self::device((string) $request->userAgent()),
            'referrer_host' => self::referrerHost($request),
            'country' => $this->geoIp->countryFor($request),
            'created_at' => now(),
        ];
    }

    /** Same visitor + same day = same hash; it changes every day and cannot be turned back into an IP. */
    public static function visitorHash(string $ip, string $userAgent, string $day): string
    {
        return substr(hash_hmac('sha256', "{$ip}|{$userAgent}|{$day}", (string) config('app.key')), 0, 16);
    }

    public static function device(string $userAgent): string
    {
        return match (true) {
            (bool) preg_match('/ipad|tablet|kindle|silk|playbook|android(?!.*mobile)/i', $userAgent) => 'tablet',
            (bool) preg_match('/mobi|iphone|ipod|android|blackberry|opera mini|iemobile/i', $userAgent) => 'mobile',
            default => 'desktop',
        };
    }

    /** Only external sites, without "www.". */
    public static function referrerHost(Request $request): ?string
    {
        $host = strtolower((string) parse_url((string) $request->headers->get('referer'), PHP_URL_HOST));
        if ($host === '' || $host === strtolower($request->getHost())) {
            return null;
        }

        return Str::limit(preg_replace('/^www\./', '', $host), 250, '');
    }
}
