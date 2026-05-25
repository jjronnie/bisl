<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\TransactionDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionDocumentFactory extends Factory
{
    protected $model = TransactionDocument::class;

    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory(),
            'name' => fake()->word(),
            'notes' => fake()->optional()->sentence(),
            'file_path' => 'transaction_documents/'.fake()->uuid().'.pdf',
        ];
    }
}
