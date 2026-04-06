<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CategorySeeder::class);

        // Demo user
        $user = User::firstOrCreate(
            ['email' => 'demo@financeapp.com'],
            [
                'name' => 'Demo User',
                'password' => bcrypt('password'),
                'theme' => 'light',
            ]
        );

        // Seed demo transactions for the last 6 months
        if (Transaction::where('user_id', $user->id)->count() === 0) {
            $this->seedDemoTransactions($user->id);
        }
    }

    private function seedDemoTransactions(int $userId): void
    {
        $categories = Category::pluck('id', 'name');

        $transactions = [
            // Incomes
            ['type' => 'income',  'amount' => 5000.00, 'category' => 'Salário',    'description' => 'Salário mensal',        'days_ago' => 0],
            ['type' => 'income',  'amount' => 800.00,  'category' => 'Freelance',  'description' => 'Projeto website',       'days_ago' => 5],
            ['type' => 'income',  'amount' => 5000.00, 'description' => 'Salário mensal',        'category' => 'Salário',    'days_ago' => 30],
            ['type' => 'income',  'amount' => 5000.00, 'description' => 'Salário mensal',        'category' => 'Salário',    'days_ago' => 60],
            ['type' => 'income',  'amount' => 1200.00, 'description' => 'Projeto app mobile',    'category' => 'Freelance',  'days_ago' => 45],

            // Expenses this month
            ['type' => 'expense', 'amount' => 1200.00, 'category' => 'Moradia',      'description' => 'Aluguel',                     'days_ago' => 1],
            ['type' => 'expense', 'amount' => 350.00,  'category' => 'Alimentação',  'description' => 'Supermercado',                 'days_ago' => 2],
            ['type' => 'expense', 'amount' => 89.90,   'category' => 'Transporte',   'description' => 'Gasolina',                     'days_ago' => 3],
            ['type' => 'expense', 'amount' => 150.00,  'category' => 'Lazer',        'description' => 'Cinema e jantar',              'days_ago' => 4],
            ['type' => 'expense', 'amount' => 45.00,   'category' => 'Remédios',     'description' => 'Farmácia',                     'days_ago' => 6],
            ['type' => 'expense', 'amount' => 280.00,  'category' => 'Máquina de Bichos de Pelúcia', 'description' => 'Fichas na máquina', 'days_ago' => 7],
            ['type' => 'expense', 'amount' => 120.00,  'category' => 'Esporte',      'description' => 'Academia mensal',              'days_ago' => 8],
            ['type' => 'expense', 'amount' => 200.00,  'category' => 'Alimentação',  'description' => 'Restaurantes',                 'days_ago' => 10],
            ['type' => 'expense', 'amount' => 75.00,   'category' => 'Transporte',   'description' => 'Uber',                         'days_ago' => 12],

            // Last month
            ['type' => 'expense', 'amount' => 1200.00, 'category' => 'Moradia',      'description' => 'Aluguel',                     'days_ago' => 31],
            ['type' => 'expense', 'amount' => 420.00,  'category' => 'Alimentação',  'description' => 'Supermercado',                 'days_ago' => 32],
            ['type' => 'expense', 'amount' => 320.00,  'category' => 'Máquina de Bichos de Pelúcia', 'description' => 'Fichas',       'days_ago' => 35],
            ['type' => 'expense', 'amount' => 98.00,   'category' => 'Transporte',   'description' => 'Combustível',                  'days_ago' => 38],
            ['type' => 'expense', 'amount' => 180.00,  'category' => 'Lazer',        'description' => 'Show e bar',                   'days_ago' => 40],
        ];

        foreach ($transactions as $t) {
            Transaction::create([
                'user_id' => $userId,
                'category_id' => $categories[$t['category']],
                'type' => $t['type'],
                'amount' => $t['amount'],
                'description' => $t['description'],
                'date' => now()->subDays($t['days_ago'])->format('Y-m-d'),
            ]);
        }
    }
}
