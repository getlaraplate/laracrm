<?php

declare(strict_types=1);

namespace Modules\CRM\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\CRM\Casts\CounterpartyKind;
use Modules\CRM\Models\Counterparty;

/**
 * @extends Factory<Counterparty>
 */
final class CounterpartyFactory extends Factory
{
    /**
     * @var class-string<Counterparty>
     */
    protected $model = Counterparty::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'kind' => CounterpartyKind::Company->value,
            'tax_id' => (string) $this->faker->unique()->numerify('###########'),
            'vat_number' => 'IT' . $this->faker->unique()->numerify('###########'),
            'fiscal_country' => 'IT',
            'address_line' => $this->faker->streetAddress(),
            'postal_code' => (string) $this->faker->numerify('#####'),
            'city' => $this->faker->city(),
            'province' => mb_strtoupper($this->faker->lexify('??')),
            'country' => 'IT',
        ];
    }

    public function company(): self
    {
        return $this->state(fn (array $attributes): array => ['kind' => CounterpartyKind::Company->value]);
    }

    public function person(): self
    {
        return $this->state(fn (array $attributes): array => [
            'kind' => CounterpartyKind::Person->value,
            'name' => $this->faker->unique()->name(),
            'vat_number' => null,
            'tax_id' => mb_strtoupper($this->faker->unique()->bothify('??????##?##?###?')),
        ]);
    }
}
