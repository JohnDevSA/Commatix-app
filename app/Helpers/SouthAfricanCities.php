<?php

namespace App\Helpers;

class SouthAfricanCities
{
    /**
     * Get all South African cities grouped by province code
     *
     * @return array<string, array<string>>
     */
    public static function getByProvince(): array
    {
        return [
            'GP' => [
                'Johannesburg',
                'Pretoria',
                'Sandton',
                'Midrand',
                'Centurion',
                'Randburg',
                'Roodepoort',
                'Kempton Park',
                'Benoni',
                'Boksburg',
                'Germiston',
                'Springs',
                'Krugersdorp',
                'Alberton',
                'Soweto',
            ],
            'WC' => [
                'Cape Town',
                'Stellenbosch',
                'Paarl',
                'George',
                'Mossel Bay',
                'Knysna',
                'Somerset West',
                'Hermanus',
                'Worcester',
                'Swellendam',
                'Oudtshoorn',
                'Plettenberg Bay',
            ],
            'KZN' => [
                'Durban',
                'Pietermaritzburg',
                'Richards Bay',
                'Newcastle',
                'Ballito',
                'Umhlanga',
                'Port Shepstone',
                'Ladysmith',
                'Empangeni',
                'Scottburgh',
            ],
            'EC' => [
                'Gqeberha',
                'Port Elizabeth',
                'East London',
                'Mthatha',
                'Uitenhage',
                'Grahamstown',
                'King Williams Town',
                'Queenstown',
                'Port Alfred',
            ],
            'FS' => [
                'Bloemfontein',
                'Welkom',
                'Bethlehem',
                'Kroonstad',
                'Sasolburg',
                'Parys',
            ],
            'LP' => [
                'Polokwane',
                'Tzaneen',
                'Mokopane',
                'Lebowakgomo',
                'Musina',
                'Thohoyandou',
            ],
            'MP' => [
                'Mbombela',
                'Nelspruit',
                'Witbank',
                'Emalahleni',
                'Secunda',
                'Middelburg',
                'Ermelo',
                'Standerton',
            ],
            'NC' => [
                'Kimberley',
                'Upington',
                'Springbok',
                'De Aar',
                'Kuruman',
            ],
            'NW' => [
                'Rustenburg',
                'Mahikeng',
                'Potchefstroom',
                'Klerksdorp',
                'Brits',
                'Vryburg',
            ],
        ];
    }

    /**
     * Get cities for a specific province
     *
     * @param string $provinceCode Province code (GP, WC, KZN, etc.)
     * @return array<string>
     */
    public static function getForProvince(string $provinceCode): array
    {
        return self::getByProvince()[$provinceCode] ?? [];
    }

    /**
     * Get all cities as a flat array (useful for validation or searching all cities)
     *
     * @return array<string>
     */
    public static function getAll(): array
    {
        $cities = [];
        foreach (self::getByProvince() as $provinceCities) {
            $cities = array_merge($cities, $provinceCities);
        }
        return array_unique($cities);
    }

    /**
     * Get cities formatted for Filament Select options
     *
     * @param string|null $provinceCode Optional province code to filter by
     * @return array<string, string>
     */
    public static function getSelectOptions(?string $provinceCode = null): array
    {
        if ($provinceCode) {
            $cities = self::getForProvince($provinceCode);
        } else {
            $cities = self::getAll();
        }

        sort($cities);

        return array_combine($cities, $cities);
    }
}
