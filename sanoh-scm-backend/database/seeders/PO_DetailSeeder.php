<?php

namespace Database\Seeders;

use App\Models\PO_Detail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PO_DetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create dummy data
        for ($i=0; $i <= 10; $i++) {
            # code...
            PO_Detail::create([
                'po_detail_no' => Str::random(10),
                'po_no' => '0VVOPkvq27',
                'po_line' => rand(2000, 2024),
                'po_sequence' => rand(1, 1000),
                'item_code' => Str::random(10),
                'code_item_type' => Str::random(10),
                'bp_part_no' => Str::random(10),
                'bp_part_name' => Str::random(10),
                'item_desc_a' => Str::random(10),
                'item_desc_b' => Str::random(10),
                'planned_receipt_date' => Carbon::now(),
                'po_qty' => rand(200, 400),
                'receipt_qty' => rand(100, 200),
                'invoice_qty' => rand(200, 400),
                'purchase_unit' => Str::random(10),
                'price' => rand(10000, 12000),
                'amount' => rand(200, 400),
            ]);
        }
    }
}
