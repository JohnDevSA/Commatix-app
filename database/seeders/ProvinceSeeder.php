<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds all 9 South African provinces with their codes and major cities.
     */
    public function run(): void
    {
        $provinces = [
            [
                'code' => 'EC',
                'name' => 'Eastern Cape',
                'major_cities' => ['Port Elizabeth', 'East London', 'Mthatha', 'Grahamstown', 'Queenstown'],
            ],
            [
                'code' => 'FS',
                'name' => 'Free State',
                'major_cities' => ['Bloemfontein', 'Welkom', 'Kroonstad', 'Sasolburg', 'Bethlehem'],
            ],
            [
                'code' => 'GP',
                'name' => 'Gauteng',
                'major_cities' => ['Johannesburg', 'Pretoria', 'Soweto', 'Midrand', 'Centurion', 'Sandton', 'Randburg'],
            ],
            [
                'code' => 'KZN',
                'name' => 'KwaZulu-Natal',
                'major_cities' => ['Durban', 'Pietermaritzburg', 'Newcastle', 'Richards Bay', 'Ladysmith'],
            ],
            [
                'code' => 'LP',
                'name' => 'Limpopo',
                'major_cities' => ['Polokwane', 'Tzaneen', 'Thohoyandou', 'Lebowakgomo', 'Musina'],
            ],
            [
                'code' => 'MP',
                'name' => 'Mpumalanga',
                'major_cities' => ['Nelspruit', 'Witbank', 'Secunda', 'Middelburg', 'Standerton'],
            ],
            [
                'code' => 'NC',
                'name' => 'Northern Cape',
                'major_cities' => ['Kimberley', 'Upington', 'Kuruman', 'De Aar', 'Springbok'],
            ],
            [
                'code' => 'NW',
                'name' => 'North West',
                'major_cities' => ['Mahikeng', 'Rustenburg', 'Potchefstroom', 'Klerksdorp', 'Brits'],
            ],
            [
                'code' => 'WC',
                'name' => 'Western Cape',
                'major_cities' => ['Cape Town', 'Stellenbosch', 'Paarl', 'George', 'Worcester', 'Hermanus'],
            ],
        ];

        foreach ($provinces as $province) {
            Province::updateOrCreate(
                ['code' => $province['code']],
                $province
            );
        }

        $this->command->info('✅ Seeded '.count($provinces).' South African provinces');
    }
}
