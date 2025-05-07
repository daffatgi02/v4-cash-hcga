<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fund;

class FundSeeder extends Seeder
{
    public function run()
    {
        $funds = [
            [
                'name' => 'Rekening Utama HCGA',
                'description' => 'Rekening bank utama divisi HCGA',
                'type' => 'rekening_utama',
                'initial_balance' => 0,
            ],
            [
                'name' => 'Kas Kecil HCGA',
                'description' => 'Dana tunai untuk keperluan sehari-hari',
                'type' => 'kas_kecil',
                'initial_balance' => 0,
            ],
            [
                'name' => 'Dana Operasional HCGA',
                'description' => 'Dana rutin bulanan',
                'type' => 'dana_operasional',
                'initial_balance' => 0,
            ],
        ];

        foreach ($funds as $fund) {
            Fund::create($fund);
        }
    }
}
