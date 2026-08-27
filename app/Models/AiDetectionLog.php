<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiDetectionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'confidence',
        'inference_time_ms',
        'fps',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
