<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Food',
            'Transportation',
            'Entertainment',
            'Shopping',
            'Bills',
            'Healthcare',
            'Education',
            'Travel',
            'Other',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate([
                'name' => $category,
            ]);
        }
    }
}
