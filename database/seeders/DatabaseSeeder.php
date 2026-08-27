<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProductSeeder::class,
        ]);

        // Seed default settings
        \App\Models\Setting::setVal('store_name', 'UMKM Aneka Kue Pak Yanto');
        \App\Models\Setting::setVal('store_address', 'Jl. Raya Kaliurang KM 10, Sleman, Yogyakarta');
        \App\Models\Setting::setVal('printer_name', 'POS-80 Thermal Printer');
        \App\Models\Setting::setVal('confidence_threshold', '0.90');
        \App\Models\Setting::setVal('camera_device_id', 'default');
        \App\Models\Setting::setVal('theme_mode', 'light');
        \App\Models\Setting::setVal('ai_model', 'best.onnx');

        User::factory()->create([
            'name' => 'Adnan Kasir',
            'email' => 'kasir@kasirpintar.test',
            'password' => bcrypt('password'),
        ]);
    }
}
