<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class PlatformSetting extends Model
{
    public const KEY_PWD_TAX_RATE = 'pwd_tax_deduction_rate';

    public const DEFAULT_PWD_TAX_RATE = 20.0;

    protected $fillable = [
        'key',
        'value',
        'updated_by',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        if (! Schema::hasTable('platform_settings')) {
            return $default;
        }

        $record = static::query()->where('key', $key)->first();

        return $record !== null ? $record->value : $default;
    }

    public static function setValue(string $key, mixed $value, ?int $updatedBy = null): static
    {
        return static::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'updated_by' => $updatedBy,
            ]
        );
    }

    public static function pwdTaxDeductionRate(): float
    {
        $raw = static::getValue(self::KEY_PWD_TAX_RATE, self::DEFAULT_PWD_TAX_RATE);
        $num = is_numeric($raw) ? (float) $raw : self::DEFAULT_PWD_TAX_RATE;

        return max(0.0, min(100.0, $num));
    }

    public static function pwdTaxDeductionDecimal(): float
    {
        return round(static::pwdTaxDeductionRate() / 100.0, 4);
    }

    public static function pwdTaxDeductionLabel(): string
    {
        $rate = static::pwdTaxDeductionRate();

        return (floor($rate) == $rate ? (string) (int) $rate : rtrim(rtrim(number_format($rate, 2, '.', ''), '0'), '.')).'%';
    }
}
