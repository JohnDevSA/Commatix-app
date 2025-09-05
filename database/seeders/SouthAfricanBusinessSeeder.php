<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SouthAfricanBusinessSeeder extends Seeder
{
    public function run(): void
    {
        // Insert SA Province data
        $this->seedProvinces();

        // Insert SA Industry Classifications (SIC Codes)
        $this->seedIndustryClassifications();

        // Insert SA Company Types
        $this->seedCompanyTypes();

        $this->command->info('✅ South African business data seeded');
    }

    private function seedProvinces(): void
    {
        $provinces = [
            ['code' => 'gauteng', 'name' => 'Gauteng', 'major_cities' => json_encode(['Johannesburg', 'Pretoria', 'Randburg', 'Sandton', 'Centurion'])],
            ['code' => 'western_cape', 'name' => 'Western Cape', 'major_cities' => json_encode(['Cape Town', 'Stellenbosch', 'Paarl', 'George'])],
            ['code' => 'kwazulu_natal', 'name' => 'KwaZulu-Natal', 'major_cities' => json_encode(['Durban', 'Pietermaritzburg', 'Newcastle'])],
            ['code' => 'eastern_cape', 'name' => 'Eastern Cape', 'major_cities' => json_encode(['Port Elizabeth', 'East London', 'Uitenhage'])],
            ['code' => 'northern_cape', 'name' => 'Northern Cape', 'major_cities' => json_encode(['Kimberley', 'Upington'])],
            ['code' => 'free_state', 'name' => 'Free State', 'major_cities' => json_encode(['Bloemfontein', 'Welkom'])],
            ['code' => 'limpopo', 'name' => 'Limpopo', 'major_cities' => json_encode(['Polokwane', 'Tzaneen'])],
            ['code' => 'mpumalanga', 'name' => 'Mpumalanga', 'major_cities' => json_encode(['Nelspruit', 'Witbank'])],
            ['code' => 'north_west', 'name' => 'North West', 'major_cities' => json_encode(['Mahikeng', 'Klerksdorp'])],
        ];

        foreach ($provinces as $province) {
            DB::table('sa_provinces')->insertOrIgnore($province);
        }
    }

    private function seedIndustryClassifications(): void
    {
        $industries = [
            ['sic_code' => '01110', 'description' => 'Growing of cereals and other crops', 'sector' => 'Agriculture'],
            ['sic_code' => '05200', 'description' => 'Coal mining', 'sector' => 'Mining'],
            ['sic_code' => '10111', 'description' => 'Processing and preserving of meat', 'sector' => 'Manufacturing'],
            ['sic_code' => '23100', 'description' => 'Manufacture of glass and glass products', 'sector' => 'Manufacturing'],
            ['sic_code' => '35110', 'description' => 'Electric power generation', 'sector' => 'Utilities'],
            ['sic_code' => '41001', 'description' => 'Development of building projects', 'sector' => 'Construction'],
            ['sic_code' => '45300', 'description' => 'Sale, maintenance and repair of motor vehicle parts', 'sector' => 'Trade'],
            ['sic_code' => '55101', 'description' => 'Short term accommodation', 'sector' => 'Accommodation'],
            ['sic_code' => '61100', 'description' => 'Wired telecommunications activities', 'sector' => 'Information Technology'],
            ['sic_code' => '64110', 'description' => 'Central banking', 'sector' => 'Financial Services'],
            ['sic_code' => '68201', 'description' => 'Letting and operating of own or leased real estate', 'sector' => 'Real Estate'],
            ['sic_code' => '69101', 'description' => 'Legal activities', 'sector' => 'Professional Services'],
            ['sic_code' => '85321', 'description' => 'Technical and vocational secondary education', 'sector' => 'Education'],
            ['sic_code' => '86101', 'description' => 'Hospital activities', 'sector' => 'Health Care'],
        ];

        foreach ($industries as $industry) {
            DB::table('sa_industry_classifications')->insertOrIgnore($industry);
        }
    }

    private function seedCompanyTypes(): void
    {
        $companyTypes = [
            ['code' => 'pty_ltd', 'name' => 'Private Company (Pty Ltd)', 'description' => 'Private company limited by shares'],
            ['code' => 'public', 'name' => 'Public Company Ltd', 'description' => 'Public company limited by shares'],
            ['code' => 'close_corp', 'name' => 'Close Corporation (CC)', 'description' => 'Close corporation (legacy structure)'],
            ['code' => 'partnership', 'name' => 'Partnership', 'description' => 'Partnership agreement'],
            ['code' => 'sole_prop', 'name' => 'Sole Proprietorship', 'description' => 'Individual trading as business'],
            ['code' => 'npo', 'name' => 'Non-Profit Organisation', 'description' => 'Non-profit organisation'],
            ['code' => 'trust', 'name' => 'Trust', 'description' => 'Trust structure'],
        ];

        foreach ($companyTypes as $type) {
            DB::table('sa_company_types')->insertOrIgnore($type);
        }
    }
}
