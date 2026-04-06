<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Lazer',                      'color' => '#8B5CF6', 'icon' => 'sparkles',       'is_default' => true],
            ['name' => 'Esporte',                    'color' => '#16A34A', 'icon' => 'trophy',          'is_default' => true],
            ['name' => 'Remédios',                   'color' => '#EF4444', 'icon' => 'heart',           'is_default' => true],
            ['name' => 'Transporte',                 'color' => '#F59E0B', 'icon' => 'truck',           'is_default' => true],
            ['name' => 'Máquina de Bichos de Pelúcia', 'color' => '#EC4899', 'icon' => 'star',           'is_default' => true],
            ['name' => 'Alimentação',                'color' => '#F97316', 'icon' => 'shopping-cart',   'is_default' => true],
            ['name' => 'Moradia',                    'color' => '#0EA5E9', 'icon' => 'home',            'is_default' => true],
            ['name' => 'Salário',                    'color' => '#22C55E', 'icon' => 'currency-dollar', 'is_default' => true],
            ['name' => 'Freelance',                  'color' => '#3B82F6', 'icon' => 'briefcase',       'is_default' => true],
            ['name' => 'Outros',                     'color' => '#64748B', 'icon' => 'tag',             'is_default' => true],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
