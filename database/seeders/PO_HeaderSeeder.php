<?php

namespace Database\Seeders;

use App\Models\PO_Header;
use Illuminate\Support\Str;
use Ramsey\Uuid\Type\Integer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Carbon;

class PO_HeaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create dummy data

        # code...
        PO_Header::create([
            'po_no' => "07TgGeZhCN",
            'supplier_code' => 'APFEY',
            'supplier_name' => Str::random(10),
            'po_type_desc' => Str::random(10),
            'po_date' => Carbon::now(),
            'po_year' => rand(2000, 2024),
            'po_period' => rand(1, 1000),
            'po_status' => Str::random(10),
            'references_1' => Str::random(10),
            'references_2' => Str::random(10),
            'attn_name' => Str::random(10),
            'po_currency' => Str::random(10),
            'pr_no' => (string)rand(2000, 2024),
            'planned_receipt_date' => Carbon::now(),
            'payment_term' => "45D",
            'po_origin' => Str::random(10),
            'po_revision_no' => rand(1, 4),
            'po_revision_date' => Carbon::now(),
            'response' => Str::random(10),
        ]);
    }
}
