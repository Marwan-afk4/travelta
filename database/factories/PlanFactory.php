<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'=>$this->faker->randomElement(['Basic', 'Standard', 'Premium']),
            'description'=>$this->faker->randomElement(['Basic', 'Standard', 'Premium']),
            'user_limit'=>$this->faker->randomElement([15,16,7,8,5]),
            'branch_limit'=>$this->faker->randomElement([15,16,7,8,5]),
            'period_in_days'=>$this->faker->randomElement([30,60,90,120,150]),
            'module_type'=>$this->faker->randomElement(['hrm','crm']),
            'price'=>$this->faker->randomElement([300,400,500,600,700]),
            'discount_type'=>$this->faker->randomElement(['fixed','percentage']),
            'price_after_discount'=>$this->faker->randomElement([200,300]),
            'admin_cost'=>$this->faker->randomElement([200,300]),
            'branch_cost'=>$this->faker->randomElement([400,500]),
        ];
    }
}
