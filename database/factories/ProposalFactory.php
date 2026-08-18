<?php

namespace Whilesmart\Proposals\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Whilesmart\Proposals\Enums\ProposalStatus;
use Whilesmart\Proposals\Models\Proposal;

class ProposalFactory extends Factory
{
    protected $model = Proposal::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'status' => ProposalStatus::Draft->value,
            'sections' => [
                ['type' => 'summary', 'title' => 'Summary', 'body' => $this->faker->paragraph()],
                ['type' => 'scope', 'title' => 'Scope', 'body' => $this->faker->paragraph()],
            ],
        ];
    }
}
