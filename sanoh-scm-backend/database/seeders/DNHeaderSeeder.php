<?php

namespace Database\Seeders;

use App\Models\DNHeader;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DNHeaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create dummy data
        for ($i=0; $i <= 10; $i++) {
            # code...
            DNHeader::create([
                'no_dn' => Str::random(10),
                'po_no' => '0VVOPkvq27',
                'dn_created_date' => Carbon::now(),
                'dn_year' => rand(2000, 2024),
                'dn_period' => rand(1, 4),
                'plan_delivery_date' => Carbon::now(),
                'plan_delivery_time' => Carbon::now(),
                'status_desc' => Str::random(10),
            ]);
        }
    }
}
