<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeSurface;

class TypeSurfaceSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['title' => 'Flag'],
            ['title' => 'Mini flag'],
            ['title' => 'Banner'],
        ];

        foreach ($types as $type) {
            TypeSurface::create($type);
        }
    }
}
