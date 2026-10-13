<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use MaxMind\Db\Reader;
use Throwable;

class UpdateGeoIpDatabase extends Command
{
    protected $signature = 'analytics:update-geoip';

    protected $description = 'Download DB-IP\'s free "IP to Country Lite" database (used to show visitor countries; IPs are never stored)';

    public function handle(): int
    {
        $target = config('analytics.geoip_database');
        File::ensureDirectoryExists(dirname($target));

        // DB-IP publishes a new file each month; early in the month it may not be out yet.
        foreach ([now(), now()->subMonthNoOverflow()] as $month) {
            if ($this->install($month, $target)) {
                $this->info("GeoIP database updated ({$month->format('F Y')} edition). IP geolocation by DB-IP (https://db-ip.com).");

                return self::SUCCESS;
            }
        }

        $this->error('Could not download a valid GeoIP database; the existing one (if any) was kept.');

        return self::FAILURE;
    }

    private function install(Carbon $month, string $target): bool
    {
        $url = sprintf('https://download.db-ip.com/free/dbip-country-lite-%s.mmdb.gz', $month->format('Y-m'));
        $download = "{$target}.download.gz";
        $candidate = "{$target}.new";

        try {
            $this->line("Downloading {$url}");
            if (! Http::timeout(300)->sink($download)->get($url)->successful()) {
                return false;
            }

            $this->gunzip($download, $candidate);
            if (! $this->isValid($candidate)) {
                $this->warn('Downloaded file failed validation.');

                return false;
            }

            // Swap in only a validated file, in one step, so lookups never see a half-written database.
            return rename($candidate, $target);
        } catch (Throwable $e) {
            $this->warn($e->getMessage());

            return false;
        } finally {
            File::delete([$download, $candidate]);
        }
    }

    private function gunzip(string $from, string $to): void
    {
        $in = gzopen($from, 'rb');
        $out = fopen($to, 'wb');
        while (! gzeof($in)) {
            fwrite($out, gzread($in, 1 << 20));
        }
        gzclose($in);
        fclose($out);
    }

    private function isValid(string $path): bool
    {
        try {
            $reader = new Reader($path);
            $valid = ($reader->get('8.8.8.8')['country']['iso_code'] ?? null) === 'US'
                && ($reader->get('41.59.0.1')['country']['iso_code'] ?? null) !== null;
            $reader->close();

            return $valid;
        } catch (Throwable) {
            return false;
        }
    }
}
