<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CategoryGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
final class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'manager_id' => User::factory(),
            'groups' => fake()->randomElements(CategoryGroup::cases(), count: 1),
            'required' => true,
        ];
    }
}
