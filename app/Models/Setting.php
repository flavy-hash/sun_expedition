<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'array',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        // Values are wrapped as {"v": …} so strings, lists and nulls all round-trip through the JSON column.
        $stored = static::query()->where('key', $key)->first()?->value;

        return is_array($stored) && array_key_exists('v', $stored) ? ($stored['v'] ?? $default) : $default;
    }

    public static function put(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => ['v' => $value]]);
    }

    /** Bank accounts with every field needed to pay, in the order they were entered. */
    public static function bankAccounts(): array
    {
        return collect(static::get('bank_accounts', []))
            ->filter(fn ($account) => is_array($account) && filled($account['bank_name'] ?? null) && filled($account['account_name'] ?? null) && filled($account['account_number'] ?? null))
            ->values()
            ->all();
    }
}
