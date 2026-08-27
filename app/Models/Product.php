<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'coco_class',
        'category_id',
        'stock',
        'image_url',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function detectionLogs(): HasMany
    {
        return $this->hasMany(AiDetectionLog::class);
    }

    /**
     * Compute average AI detection confidence for this product.
     */
    public function getAverageConfidenceAttribute(): float
    {
        return (float) ($this->detectionLogs()->avg('confidence') ?? 0.0);
    }

    /**
     * Accessor: resolve image_url.
     * Handles:
     *  - Base64 data URI (data:image/...) → returned as-is
     *  - External URL (http/https, not our server) → returned as-is
     *  - Old full URL from our server → extract relative path + asset()
     *  - Relative path (uploads/products/...) → asset()
     *  - Empty/null → default placeholder
     */
    public function getImageUrlAttribute($value): string
    {
        if (!$value) {
            return '/logo.png';
        }

        // Base64 data URI — use directly in <img src>
        if (str_starts_with($value, 'data:')) {
            return $value;
        }

        // Full URL stored
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            // If it contains internal upload/logo paths, convert to root-relative path
            if (str_contains($value, '/uploads/') || str_contains($value, 'logo.png')) {
                $parsed = parse_url($value);
                return '/' . ltrim($parsed['path'] ?? '', '/');
            }
            // External image URL from third-party host
            return $value;
        }

        // Standard relative path (e.g. uploads/products/...)
        return '/' . ltrim($value, '/');
    }
}
