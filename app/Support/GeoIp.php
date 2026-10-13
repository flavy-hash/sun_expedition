<?php

namespace App\Support;

use Illuminate\Http\Request;
use MaxMind\Db\Reader;
use Throwable;

/** Country code for a request. Only the two-letter code is ever returned; the IP is never stored. */
class GeoIp
{
    private ?Reader $reader = null;

    private bool $readerTried = false;

    public function countryFor(Request $request): ?string
    {
        // Behind Cloudflare the edge already knows the country. XX = unknown, T1 = Tor.
        $header = strtoupper((string) $request->header('CF-IPCountry'));
        if (preg_match('/^[A-Z]{2}$/', $header) && ! in_array($header, ['XX', 'T1'], true)) {
            return $header;
        }

        return $this->lookup((string) $request->ip());
    }

    public function lookup(string $ip): ?string
    {
        // Private and reserved ranges (localhost, office LAN…) have no country.
        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return null;
        }

        try {
            $code = $this->reader()?->get($ip)['country']['iso_code'] ?? null;
        } catch (Throwable) {
            return null;
        }

        return is_string($code) && preg_match('/^[A-Z]{2}$/', $code) ? $code : null;
    }

    private function reader(): ?Reader
    {
        if (! $this->readerTried) {
            $this->readerTried = true;
            $path = config('analytics.geoip_database');
            $this->reader = is_file($path) ? new Reader($path) : null;
        }

        return $this->reader;
    }
}
