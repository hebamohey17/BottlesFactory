<?php

namespace Database\Seeders;

use App\Models\BottleSize;
use App\Models\Ingredient;
use App\Models\WaterType;
use App\Support\CompositionMatrix;
use Illuminate\Database\Seeder;


class BottleCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = $this->seedIngredients();
        $sizes = $this->seedBottleSizes();

        $this->seedWaterTypeCompositions($ingredients, $sizes);
    }

    /**
     * @return array<string, Ingredient>
     */
    private function seedIngredients(): array
    {
     
        $catalog = [
            'صوديوم' => '0.042',
            'بوتاسيوم' => '0.068',
            'مغنيسيوم' => '0.055',
            'كالسيوم' => '0.048',
            'بيكربونات' => '0.031',
            'كلوريد' => '0.029',
            'سلفات' => '0.035',
            'سيليكا طبيعية' => '0.022',
            'فلورايد' => '0.095',
            'ليثيوم' => '0.112',
        ];

        $out = [];
        foreach ($catalog as $name => $price) {
            $out[$name] = Ingredient::query()->firstOrCreate(
                ['name' => $name],
                ['price_per_ml' => $price],
            );
        }

        return $out;
    }

    /**
     * @return array<string, BottleSize>
     */
    private function seedBottleSizes(): array
    {
        $defs = [
            '250' => ['name' => '250 مل — عبوة صغيرة', 'capacity_ml' => 250],
            '330' => ['name' => '330 مل — قنينة قياسية', 'capacity_ml' => 330],
            '500' => ['name' => '500 مل — عبوة متوسطة', 'capacity_ml' => 500],
            '600' => ['name' => '600 مل — عبوة رياضية', 'capacity_ml' => 600],
            '750' => ['name' => '750 مل — عبوة كبيرة', 'capacity_ml' => 750],
            '1000' => ['name' => '1 لتر', 'capacity_ml' => 1000],
            '1500' => ['name' => '1.5 لتر — عائلي', 'capacity_ml' => 1500],
        ];

        $out = [];
        foreach ($defs as $key => $row) {
            $out[$key] = BottleSize::query()->firstOrCreate(
                ['capacity_ml' => $row['capacity_ml']],
                ['name' => $row['name']],
            );
        }

        return $out;
    }

    /**
     * @param  array<string, Ingredient>  $i
     * @param  array<string, BottleSize>  $s
     */
    private function seedWaterTypeCompositions(array $i, array $s): void
    {
        $mineralNatural = WaterType::query()->firstOrCreate(
            ['name' => 'مياه معدنية طبيعية — منبع'],
        );

        CompositionMatrix::sync($mineralNatural, [
            [
                'bottle_size_id' => $s['330']->id,
                'lines' => [
                    ['ingredient_id' => $i['صوديوم']->id, 'amount_ml' => 72],
                    ['ingredient_id' => $i['بوتاسيوم']->id, 'amount_ml' => 64],
                    ['ingredient_id' => $i['مغنيسيوم']->id, 'amount_ml' => 58],
                    ['ingredient_id' => $i['كالسيوم']->id, 'amount_ml' => 52],
                    ['ingredient_id' => $i['سيليكا طبيعية']->id, 'amount_ml' => 64],
                ],
            ],
            [
                'bottle_size_id' => $s['500']->id,
                'lines' => [
                    ['ingredient_id' => $i['صوديوم']->id, 'amount_ml' => 118],
                    ['ingredient_id' => $i['بوتاسيوم']->id, 'amount_ml' => 102],
                    ['ingredient_id' => $i['مغنيسيوم']->id, 'amount_ml' => 88],
                    ['ingredient_id' => $i['كالسيوم']->id, 'amount_ml' => 82],
                    ['ingredient_id' => $i['بيكربونات']->id, 'amount_ml' => 85],
                ],
            ],
            [
                'bottle_size_id' => $s['1500']->id,
                'lines' => [
                    ['ingredient_id' => $i['صوديوم']->id, 'amount_ml' => 315],
                    ['ingredient_id' => $i['بوتاسيوم']->id, 'amount_ml' => 292],
                    ['ingredient_id' => $i['مغنيسيوم']->id, 'amount_ml' => 284],
                    ['ingredient_id' => $i['كالسيوم']->id, 'amount_ml' => 276],
                    ['ingredient_id' => $i['كلوريد']->id, 'amount_ml' => 293],
                ],
            ],
        ]);

        $sparkling = WaterType::query()->firstOrCreate(
            ['name' => 'مياه معدنية غازية'],
        );

        CompositionMatrix::sync($sparkling, [
            [
                'bottle_size_id' => $s['330']->id,
                'lines' => [
                    ['ingredient_id' => $i['بيكربونات']->id, 'amount_ml' => 88],
                    ['ingredient_id' => $i['صوديوم']->id, 'amount_ml' => 76],
                    ['ingredient_id' => $i['بوتاسيوم']->id, 'amount_ml' => 64],
                    ['ingredient_id' => $i['مغنيسيوم']->id, 'amount_ml' => 82],
                ],
            ],
            [
                'bottle_size_id' => $s['600']->id,
                'lines' => [
                    ['ingredient_id' => $i['بيكربونات']->id, 'amount_ml' => 162],
                    ['ingredient_id' => $i['صوديوم']->id, 'amount_ml' => 144],
                    ['ingredient_id' => $i['بوتاسيوم']->id, 'amount_ml' => 126],
                    ['ingredient_id' => $i['سلفات']->id, 'amount_ml' => 138],
                ],
            ],
            [
                'bottle_size_id' => $s['1000']->id,
                'lines' => [
                    ['ingredient_id' => $i['بيكربونات']->id, 'amount_ml' => 210],
                    ['ingredient_id' => $i['صوديوم']->id, 'amount_ml' => 195],
                    ['ingredient_id' => $i['بوتاسيوم']->id, 'amount_ml' => 178],
                    ['ingredient_id' => $i['مغنيسيوم']->id, 'amount_ml' => 184],
                    ['ingredient_id' => $i['كلوريد']->id, 'amount_ml' => 193],
                ],
            ],
        ]);

        $balanced = WaterType::query()->firstOrCreate(
            ['name' => 'مياه معادن متوازنة — للرياضيين'],
        );

        CompositionMatrix::sync($balanced, [
            [
                'bottle_size_id' => $s['500']->id,
                'lines' => [
                    ['ingredient_id' => $i['صوديوم']->id, 'amount_ml' => 98],
                    ['ingredient_id' => $i['بوتاسيوم']->id, 'amount_ml' => 95],
                    ['ingredient_id' => $i['مغنيسيوم']->id, 'amount_ml' => 92],
                    ['ingredient_id' => $i['كالسيوم']->id, 'amount_ml' => 89],
                    ['ingredient_id' => $i['سلفات']->id, 'amount_ml' => 91],
                ],
            ],
            [
                'bottle_size_id' => $s['750']->id,
                'lines' => [
                    ['ingredient_id' => $i['بوتاسيوم']->id, 'amount_ml' => 144],
                    ['ingredient_id' => $i['مغنيسيوم']->id, 'amount_ml' => 139],
                    ['ingredient_id' => $i['صوديوم']->id, 'amount_ml' => 136],
                    ['ingredient_id' => $i['كالسيوم']->id, 'amount_ml' => 133],
                    ['ingredient_id' => $i['ليثيوم']->id, 'amount_ml' => 148],
                ],
            ],
        ]);

        $spring = WaterType::query()->firstOrCreate(
            ['name' => 'مياه منبع — تركيبة خفيفة'],
        );

        CompositionMatrix::sync($spring, [
            [
                'bottle_size_id' => $s['250']->id,
                'lines' => [
                    ['ingredient_id' => $i['سيليكا طبيعية']->id, 'amount_ml' => 62],
                    ['ingredient_id' => $i['كالسيوم']->id, 'amount_ml' => 59],
                    ['ingredient_id' => $i['مغنيسيوم']->id, 'amount_ml' => 57],
                    ['ingredient_id' => $i['بوتاسيوم']->id, 'amount_ml' => 60],
                ],
            ],
            [
                'bottle_size_id' => $s['750']->id,
                'lines' => [
                    ['ingredient_id' => $i['سيليكا طبيعية']->id, 'amount_ml' => 178],
                    ['ingredient_id' => $i['كالسيوم']->id, 'amount_ml' => 172],
                    ['ingredient_id' => $i['مغنيسيوم']->id, 'amount_ml' => 166],
                    ['ingredient_id' => $i['بيكربونات']->id, 'amount_ml' => 174],
                ],
            ],
        ]);

        $lowMineral = WaterType::query()->firstOrCreate(
            ['name' => 'مياه مقطرة مع إضافة معادن خفيفة'],
        );

        CompositionMatrix::sync($lowMineral, [
            [
                'bottle_size_id' => $s['500']->id,
                'lines' => [
                    ['ingredient_id' => $i['صوديوم']->id, 'amount_ml' => 118],
                    ['ingredient_id' => $i['بوتاسيوم']->id, 'amount_ml' => 112],
                    ['ingredient_id' => $i['مغنيسيوم']->id, 'amount_ml' => 104],
                    ['ingredient_id' => $i['فلورايد']->id, 'amount_ml' => 106],
                ],
            ],
        ]);
    }
}
