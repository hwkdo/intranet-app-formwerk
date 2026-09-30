<?php

declare(strict_types=1);

namespace Hwkdo\IntranetAppFormwerk\Database\Factories;

use Hwkdo\IntranetAppFormwerk\Enums\DatenabrufType;
use Hwkdo\IntranetAppFormwerk\Models\Datenabruf;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Datenabruf>
 */
class DatenabrufFactory extends Factory
{
    protected $model = Datenabruf::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'type' => fake()->randomElement(DatenabrufType::cases()),
            'key' => null,
            'call_count' => 0,
            'is_active' => true,
        ];
    }

    public function withKey(?string $key = null): static
    {
        return $this->state(fn (): array => [
            'key' => $key ?? fake()->bothify('????????????????????'),
        ]);
    }

    public function type(DatenabrufType $type): static
    {
        return $this->state(fn (): array => [
            'type' => $type,
            'name' => $type->label(),
        ]);
    }
}
