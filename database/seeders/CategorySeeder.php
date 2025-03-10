<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            'Tech News & Trends',
            'Programming & Development',
            'Gadgets & Hardware',
            'Artificial Intelligence & Machine Learning',
            'Cybersecurity & Privacy',
            'Cloud Computing & DevOps',
            'Blockchain & Cryptocurrency',
            'Tech Tutorials & How-To Guides',
            'Startup & Entrepreneurship in Tech',
            'Future of Technology'
        ];

        foreach ($categories as $category) {
            Category::create([
                'category_name' => $category,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
