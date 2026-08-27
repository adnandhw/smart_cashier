<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Change image_url from VARCHAR to MEDIUMTEXT to support base64-encoded images.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE products MODIFY image_url MEDIUMTEXT NULL');
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        // Truncate base64 values back to empty if reverting (VARCHAR 255 is too small)
        DB::statement("UPDATE products SET image_url = NULL WHERE LENGTH(image_url) > 255");
        DB::statement('ALTER TABLE products MODIFY image_url VARCHAR(255) NULL');
    }
};
