<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * php artisan db:seed
     */
    public function run(): void
    {
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(SizeSurfaceSeeder::class);
        $this->call(TypeSurfaceSeeder::class);
        $this->call(SurfaceSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(DesignSeeder::class);
        $this->call(PromotionSeeder::class);
    }
}
