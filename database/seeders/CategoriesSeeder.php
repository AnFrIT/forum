<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Акъида',
                'description' => 'Обсуждение положений исламской Акъиды (доктрины)',
                'order' => 1,
            ],
            [
                'name' => 'Тафсир',
                'description' => 'Наука "Тафсир" (Разъяснение аятов Корана)',
                'order' => 2,
            ],
            [
                'name' => 'Хадис',
                'description' => 'Наука "Хадис" (санад, матн, ривая, дирая)',
                'order' => 3,
            ],
            [
                'name' => 'Усуль аль-фикх',
                'description' => 'Наука "Усуль аль-фикх" (основы фикха)',
                'order' => 4,
            ],
            [
                'name' => 'Фикх - Исламское право',
                'description' => 'Подфорумы: Мусталят, Брак, Развод, Наследство, Ахляк, Адаб, Пища, Одежда, Ибадат, Очищение, Намаз, Пост, Закят, Хадж, Джихад, Другое',
                'order' => 5,
                'children' => [
                    ['name' => 'Мусталят', 'description' => ''],
                    ['name' => 'Брак', 'description' => ''],
                    ['name' => 'Развод', 'description' => ''],
                    ['name' => 'Наследство', 'description' => ''],
                    ['name' => 'Ахляк', 'description' => ''],
                    ['name' => 'Адаб', 'description' => ''],
                    ['name' => 'Пища', 'description' => ''],
                    ['name' => 'Одежда', 'description' => ''],
                    ['name' => 'Ибадат', 'description' => ''],
                    ['name' => 'Очищение', 'description' => ''],
                    ['name' => 'Намаз', 'description' => ''],
                    ['name' => 'Пост', 'description' => ''],
                    ['name' => 'Закят', 'description' => ''],
                    ['name' => 'Хадж', 'description' => ''],
                    ['name' => 'Джихад', 'description' => ''],
                    ['name' => 'Другое', 'description' => ''],
                ],
            ],
            [
                'name' => 'Шариатская политика',
                'description' => '',
                'order' => 6,
            ],
            [
                'name' => 'Арабский язык',
                'description' => 'Обсуждение Арабского языка (фусха)',
                'order' => 7,
            ],
            [
                'name' => 'История',
                'description' => 'История исламской уммы',
                'order' => 8,
                'children' => [
                    ['name' => 'Сподвижники', 'description' => ''],
                    ['name' => 'Праведные халифы', 'description' => ''],
                ],
            ],
            [
                'name' => 'Ученые и выдающиеся личности',
                'description' => '',
                'order' => 9,
            ],
            [
                'name' => 'Исламские группы',
                'description' => '',
                'order' => 10,
            ],
            [
                'name' => 'Разное',
                'description' => '',
                'order' => 11,
                'children' => [
                    ['name' => 'Исламские издания', 'description' => ''],
                    ['name' => 'Компьютеры и безопасность', 'description' => ''],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $categoryData['slug'] = Str::slug($categoryData['name']);
            $category = Category::create($categoryData);

            foreach ($children as $index => $childData) {
                $childData['parent_id'] = $category->id;
                $childData['slug'] = Str::slug($childData['name']);
                $childData['order'] = $index + 1;
                Category::create($childData);
            }
        }
    }
}
