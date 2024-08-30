<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Models\PartnerLocal;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PartnerLocal::create([
            'bp_code' => 'APFEY',
            'bp_name' => Str::random(10),
            'bp_status_desc' => Str::random(10),
            'bp_currency' => Str::random(10),
            'country' => Str::random(10),
            'adr_line_1' => Str::random(10),
            'adr_line_2' => Str::random(10),
            'adr_line_3' => Str::random(10),
            'adr_line_4' => Str::random(10),
            'bp_phone' => Str::random(10),
            'bp_fax' => Str::random(10)
        ]);
    }
}
