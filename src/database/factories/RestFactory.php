<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Rest;
use App\Models\Work;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rest>
 */
class RestFactory extends Factory
{
    protected $model = Rest::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('2024-01-01', 'now', 'Asia/Tokyo');
        $restFinish = $this->faker->dateTimeBetween($date, strtotime('+30 minutes'), 'Asia/Tokyo');

        return [
            'user_id' => function() {
                return User::factory()->create()->id;
            },
            'work_id' => function() {
                return Work::factory()->create()->id;
            },
            'rest_start' => $date,
            'rest_finish' => $restFinish,
        ];
    }
}
