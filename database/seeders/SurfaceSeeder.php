<?php

namespace Database\Seeders;

use App\Models\SizeSurface;
use App\Models\TypeSurface;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Seeder;
use App\Models\Surface;
use App\Models\Category;
use Illuminate\Support\Facades\Config;

class SurfaceSeeder extends Seeder {
    public function run(): void {
        // Создаем 3 поверхности с разными именами
        $surfaces = [
            ['vendor_code' => 111, 'name' => 'First surface'],
            ['vendor_code' => 112, 'name' => 'Second surface'],
            ['vendor_code' => 113, 'name' => 'Third surface']
        ];

        foreach ($surfaces as $surfaceData) {
            // Получаем случайное значение для type_surface и size_surface
            $typeSurface = TypeSurface::inRandomOrder()->first()->title;
            $sizeSurface = SizeSurface::inRandomOrder()->first()->title;

            // Создаем запись для каждой поверхности
            Surface::create([
                'vendor_code' => $surfaceData['vendor_code'],
                'name' => $surfaceData['name'],
                'type_surface' => $typeSurface,
                'size_surface' => $sizeSurface,
                'description' => null,
                'divided_bool' => true,
                'status' => [0],
                'url_images' => [],
            ]);
        }
    }
}

