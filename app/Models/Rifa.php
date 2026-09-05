<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Rifa extends Model
{
    /** @var int Número máximo de participantes na lista de ranking */
    private const MAX_RANKING = 3;

    /** @var string */
    public const STATUS_PUBLISHED = 'published';

    /** @var string */
    public const STATUS_FINISHED = 'finished';

    /** @var string */
    public const STATUS_DRAFT = 'draft';

    use HasFactory;

    protected $fillable = [
        'title',
        'thumbnail',
        'price',
        'description',
        'slug',
        'total_numbers_available',
        'buy_max',
        'buy_min',
        'raffle',
        'status',
        'progress_percentage',
        'partner_pix_key',
        'partner_name',
        'partner_split_percent',
        'expired_at',
        'ranking_buyer',
        'published_at',
    ];

    protected $appends = [
        'sold_percentage',
    ];

    public function soldPercentage(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (isset($attributes['progress_percentage']) && $attributes['progress_percentage'] !== null) {
                    return (float) $attributes['progress_percentage'];
                }

                $total = (int) ($attributes['total_numbers_available'] ?? 0);
                if ($total <= 0) {
                    return 0;
                }

                $jsonLenFunc = DB::getDriverName() === 'sqlite' ? 'json_array_length' : 'JSON_LENGTH';
                $sold = (int) $this->orders()
                    ->where('status', Order::STATUS_PAID)
                    ->sum(DB::raw("{$jsonLenFunc}(numbers_reserved)"));

                return min(100, round(($sold / $total) * 100, 1));
            }
        );
    }

    public function price(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => floatval($value)
        );
    }

    public function slug(): Attribute
    {
        return Attribute::make(
            set: fn ($value, $attributes) => $attributes['slug'] ?? $value
        );
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function winners(): HasMany
    {
        return $this->hasMany(Winner::class);
    }

    public function ranking(): Collection
    {
        $jsonLenFunc = DB::getDriverName() === 'sqlite' ? 'json_array_length' : 'JSON_LENGTH';

        return $this->orders()
            ->select('customer_fullname', DB::raw("SUM({$jsonLenFunc}(numbers_reserved)) as total_numbers"))
            ->where('status', Order::STATUS_PAID)
            ->groupBy('customer_telephone')
            ->orderBy('total_numbers', 'desc')
            ->limit(self::MAX_RANKING)
            ->get();
    }

    public function scopeAvailables(Builder $query): void
    {
        $query->where('status', Rifa::STATUS_PUBLISHED)
            ->where('published_at', '<=', now())
            ->where(function ($query) {
                $query->where('expired_at', '>', now()->format('Y-m-d H:i'))
                    ->orWhereNull('expired_at');
            });
    }
}
