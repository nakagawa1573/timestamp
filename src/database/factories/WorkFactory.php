<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Work;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Work>
 */
class WorkFactory extends Factory
{
    protected $model = Work::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userId = User::inRandomOrder()->value('id');
        
        $workStartDate = $this->faker->dateTimeBetween('2024-01-01', 'now', 'Asia/Tokyo');

        $workFinishDate = $this->faker->dateTimeBetween(
            $workStartDate->format('Y-m-d') . ' 00:00:00',
            $workStartDate->format('Y-m-d') . ' 12:00:00',
            'Asia/Tokyo'
        );

        if ($workStartDate->format('Y-m-d') !== $workFinishDate->format('Y-m-d')) {
            $workFinishDate->modify('+1 day');
            return $workFinishDate;
        }

        return [
            'user_id' => function() {
                return User::factory()->create()->id;
            },
            'work_start' => $workStartDate,
            'work_finish' => $workFinishDate,
        ];
    }
}
