<?php

namespace Database\Seeders;

use App\Models\Merchant;
use Illuminate\Database\Seeder;

class MerchantSeeder extends Seeder
{
    public function run(): void
    {
        $merchants = [
            [
                'id' => 'm_01',
                'name' => 'Holland Bakery Summarecon',
                'category' => 'Bakery & Pastry',
                'address' => 'Ruko Sinpasa Blok A-12, Summarecon Bekasi',
                'latitude' => -6.223847,
                'longitude' => 107.001248,
                'phone' => '081288991122',
                'is_verified_dkpp' => true,
                'bank_account' => 'BCA 8720192831 a/n PT Holland Bekasi',
                'rating' => 4.9,
            ],
            [
                'id' => 'm_02',
                'name' => 'Kopi Nako & Toast Grand Galaxy',
                'category' => 'Cafe & Toast',
                'address' => 'Jl. Boulevard Raya Barat No. 8, Jaka Setia, Bekasi Selatan',
                'latitude' => -6.269412,
                'longitude' => 106.974182,
                'phone' => '081399882233',
                'is_verified_dkpp' => true,
                'bank_account' => 'Mandiri 13200981293 a/n Kopi Nako Galaxy',
                'rating' => 4.8,
            ],
            [
                'id' => 'm_03',
                'name' => 'Dapur Solo Bu Titik Kranji',
                'category' => 'Restoran Tradisional',
                'address' => 'Jl. Jenderal Sudirman No. 45, Kranji, Bekasi Barat',
                'latitude' => -6.230192,
                'longitude' => 106.978211,
                'phone' => '081122334455',
                'is_verified_dkpp' => true,
                'bank_account' => 'BRI 019201928371 a/n Siti Titik',
                'rating' => 4.7,
            ],
            [
                'id' => 'm_04',
                'name' => 'Dough Deli Artisan Harapan Indah',
                'category' => 'Artisan Bakery',
                'address' => 'Ruko Sentra Niaga 2 No. 19, Medan Satria, Bekasi',
                'latitude' => -6.182910,
                'longitude' => 106.985210,
                'phone' => '081566778899',
                'is_verified_dkpp' => true,
                'bank_account' => 'BCA 7710293812 a/n Dough Deli',
                'rating' => 4.9,
            ],
        ];

        foreach ($merchants as $m) {
            Merchant::updateOrCreate(['id' => $m['id']], $m);
        }
    }
}
