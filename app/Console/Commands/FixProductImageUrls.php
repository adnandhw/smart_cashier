<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class FixProductImageUrls extends Command
{
    protected $signature   = 'products:fix-image-urls';
    protected $description = 'Convert absolute image_url values to relative paths in the products table.';

    public function handle(): int
    {
        $products = Product::whereNotNull('image_url')
            ->where('image_url', 'like', 'http%/uploads/products/%')
            ->get();

        if ($products->isEmpty()) {
            $this->info('No records need fixing. All image_urls are already relative paths.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        $fixed = 0;
        foreach ($products as $product) {
            $raw      = $product->getRawOriginal('image_url');
            $parsed   = parse_url($raw);
            $relative = ltrim($parsed['path'] ?? '', '/');

            if ($relative) {
                // Use updateQuietly to skip timestamps & model events
                $product->timestamps = false;
                $product->updateQuietly(['image_url' => $relative]);
                $fixed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done. Fixed {$fixed} product image URL(s).");

        return self::SUCCESS;
    }
}
