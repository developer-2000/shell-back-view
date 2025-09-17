<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SizeSurface;

class SizeSurfaceSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = [
            ['title' => 'H750 x W2500 х D10'],
            ['title' => 'H900 x W2964 х D10'],
        ];

        foreach ($sizes as $size) {
            SizeSurface::create($size);
        }
    }
}
