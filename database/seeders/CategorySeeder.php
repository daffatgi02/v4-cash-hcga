<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            // Kategori Pemasukan
            ['name' => 'Dana Operasional', 'description' => 'Dana rutin dari divisi Finance', 'type' => 'pemasukan'],
            ['name' => 'Pencairan Dana RKB', 'description' => 'Pencairan dana untuk kegiatan', 'type' => 'pemasukan'],
            ['name' => 'Pengembalian Piutang Staff', 'description' => 'Dana yang dikembalikan staff', 'type' => 'pemasukan'],
            ['name' => 'Dana Talangan', 'description' => 'Dana talangan antar rekening', 'type' => 'pemasukan'],
            ['name' => 'Pengembalian Dana Talangan', 'description' => 'Dana talangan yang dikembalikan', 'type' => 'pemasukan'],
            ['name' => 'Lain-lain', 'description' => 'Pemasukan lainnya', 'type' => 'pemasukan'],

            // Kategori Pengeluaran
            ['name' => 'ATK', 'description' => 'Alat Tulis Kantor', 'type' => 'pengeluaran'],
            ['name' => 'Konsumsi', 'description' => 'Makanan dan minuman', 'type' => 'pengeluaran'],
            ['name' => 'Event', 'description' => 'Pengeluaran untuk event', 'type' => 'pengeluaran'],
            ['name' => 'Transport', 'description' => 'Pengeluaran transportasi', 'type' => 'pengeluaran'],
            ['name' => 'Dana Talangan', 'description' => 'Dana talangan antar rekening', 'type' => 'pengeluaran'],
            ['name' => 'Pengembalian Dana Talangan', 'description' => 'Pengembalian dana talangan', 'type' => 'pengeluaran'],
            ['name' => 'Uang Muka Staff', 'description' => 'Uang diberikan ke staff', 'type' => 'pengeluaran'],
            ['name' => 'Utilitas', 'description' => 'Listrik, air, telepon', 'type' => 'pengeluaran'],
            ['name' => 'Pelatihan', 'description' => 'Biaya pelatihan', 'type' => 'pengeluaran'],
            ['name' => 'Lain-lain', 'description' => 'Pengeluaran lainnya', 'type' => 'pengeluaran'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
