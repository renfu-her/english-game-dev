<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => '日常生活',
                'slug' => 'daily-conversation',
                'description' => '日常生活中的英語對話和表達',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => '旅遊與交通',
                'slug' => 'travel-transport',
                'description' => '旅遊和交通相關的英語詞彙和對話',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => '商業英語',
                'slug' => 'business-english',
                'description' => '商業場合的英語表達和專業術語',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => '校園生活',
                'slug' => 'campus-life',
                'description' => '校園生活中的英語對話和學習用語',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => '健康與醫療',
                'slug' => 'health-medical',
                'description' => '健康和醫療相關的英語詞彙和對話',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
