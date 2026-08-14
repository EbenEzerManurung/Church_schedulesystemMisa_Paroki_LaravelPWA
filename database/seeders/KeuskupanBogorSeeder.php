<?php
// database/seeders/KeuskupanGerejaSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Keuskupan;
use App\Models\Gereja;

class KeuskupanGerejaSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // 1. BUAT SEMUA KEUSKUPAN
        // ============================================
        $keuskupans = [
            [
                'code' => 'BGR',
                'name' => 'Keuskupan Bogor',
                'description' => 'Keuskupan yang melayani wilayah Bogor dan sekitarnya',
                'address' => 'Jl. Kapten Muslihat No. 1, Bogor',
                'phone' => '(0251) 8324567',
                'email' => 'admin@keuskupanbogor.org',
            ],
            [
                'code' => 'AGJ',
                'name' => 'Keuskupan Agung Jakarta',
                'description' => 'Keuskupan Agung yang melayani wilayah Jakarta Raya',
                'address' => 'Jl. Katedral No. 1, Jakarta Pusat',
                'phone' => '(021) 3456789',
                'email' => 'admin@keuskupanagungjakarta.org',
            ],
            [
                'code' => 'BDG',
                'name' => 'Keuskupan Bandung',
                'description' => 'Keuskupan yang melayani wilayah Bandung dan sekitarnya',
                'address' => 'Jl. Merdeka No. 10, Bandung',
                'phone' => '(022) 4567890',
                'email' => 'admin@keuskupanbandung.org',
            ],
            [
                'code' => 'SMG',
                'name' => 'Keuskupan Agung Semarang',
                'description' => 'Keuskupan Agung yang melayani wilayah Semarang dan sekitarnya',
                'address' => 'Jl. Pandanaran No. 12, Semarang',
                'phone' => '(024) 5678901',
                'email' => 'admin@keuskupanagungsemarang.org',
            ],
            [
                'code' => 'MDN',
                'name' => 'Keuskupan Agung Medan',
                'description' => 'Keuskupan Agung yang melayani wilayah Medan dan sekitarnya',
                'address' => 'Jl. Pemuda No. 25, Medan',
                'phone' => '(061) 6789012',
                'email' => 'admin@keuskupanagungmedan.org',
            ],
        ];
        
        foreach ($keuskupans as $data) {
            Keuskupan::updateOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
        
        $this->command->info('✅ 5 Keuskupan berhasil dibuat');
        
        // ============================================
        // 2. BUAT GEREJA UNTUK KEUSKUPAN BOGOR (8 GEREJA)
        // ============================================
        $keuskupanBogor = Keuskupan::where('code', 'BGR')->first();
        
        if ($keuskupanBogor) {
            $gerejaBogor = [
                [
                    'nama' => 'Gereja Katedral Bogor (Gereja Santa Perawan Maria)',
                    'lokasi' => 'Jl. Kapten Muslihat',
                    'alamat_lengkap' => 'Gereja Santa Perawan Maria, Jl. Kapten Muslihat No. 1, Bogor',
                    'telepon' => '(0251) 8324567',
                    'email' => 'katedral@keuskupanbogor.org',
                    'pastor' => 'RD. Petrus Suwondo, Pr',
                    'jumlah_umat' => 5000,
                ],
                [
                    'nama' => 'Gereja St. Fransiskus dari Asisi',
                    'lokasi' => 'Sukasari',
                    'alamat_lengkap' => 'Gereja St. Fransiskus dari Asisi, Jl. Sukasari Raya No. 45, Bogor',
                    'telepon' => '(0251) 8345678',
                    'email' => 'fransiskus@keuskupanbogor.org',
                    'pastor' => 'RD. Antonius Sumaryono, Pr',
                    'jumlah_umat' => 3500,
                ],
                [
                    'nama' => 'Gereja St. Joannes Baptista',
                    'lokasi' => 'Parung',
                    'alamat_lengkap' => 'Gereja St. Joannes Baptista, Jl. Raya Parung No. 123, Bogor',
                    'telepon' => '(0251) 8456789',
                    'email' => 'joannes@keuskupanbogor.org',
                    'pastor' => 'RD. Ignatius Suharyono, Pr',
                    'jumlah_umat' => 2800,
                ],
                [
                    'nama' => 'Gereja St. Yakobus Rasul',
                    'lokasi' => 'Megamendung',
                    'alamat_lengkap' => 'Gereja St. Yakobus Rasul, Jl. Raya Megamendung No. 67, Bogor',
                    'telepon' => '(0251) 8567890',
                    'email' => 'yakobus@keuskupanbogor.org',
                    'pastor' => 'RD. Aloysius Widodo, Pr',
                    'jumlah_umat' => 2100,
                ],
                [
                    'nama' => 'Gereja St. Maria Fatima',
                    'lokasi' => 'Sentul City',
                    'alamat_lengkap' => 'Gereja St. Maria Fatima, Sentul City Boulevard, Bogor',
                    'telepon' => '(0251) 8678901',
                    'email' => 'fatima@keuskupanbogor.org',
                    'pastor' => 'RD. Herman Yosef, Pr',
                    'jumlah_umat' => 4200,
                ],
                [
                    'nama' => 'Gereja St. Ignatius Loyola',
                    'lokasi' => 'Semplak',
                    'alamat_lengkap' => 'Gereja St. Ignatius Loyola, Jl. Semplak No. 89, Bogor',
                    'telepon' => '(0251) 8789012',
                    'email' => 'ignatius@keuskupanbogor.org',
                    'pastor' => 'RD. Lukas Widyatmoko, Pr',
                    'jumlah_umat' => 1900,
                ],
                [
                    'nama' => 'Gereja Sta. Faustina Kowalska',
                    'lokasi' => 'Bojong Gede',
                    'alamat_lengkap' => 'Gereja Sta. Faustina Kowalska, Jl. Raya Bojong Gede No. 234, Bogor',
                    'telepon' => '(0251) 8890123',
                    'email' => 'faustina@keuskupanbogor.org',
                    'pastor' => 'RD. Kristoforus Hadi, Pr',
                    'jumlah_umat' => 3100,
                ],
                [
                    'nama' => 'Gereja St. Andreas',
                    'lokasi' => 'Sukaraja',
                    'alamat_lengkap' => 'Gereja St. Andreas, Jl. Sukaraja No. 56, Bogor',
                    'telepon' => '(0251) 8901234',
                    'email' => 'andreas@keuskupanbogor.org',
                    'pastor' => 'RD. Yohanes Subagya, Pr',
                    'jumlah_umat' => 2400,
                ],
            ];
            
            foreach ($gerejaBogor as $index => $data) {
                // Generate kode otomatis: BGR-001, BGR-002, dst
                $kode = 'BGR-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                
                Gereja::updateOrCreate(
                    [
                        'kode' => $kode,
                        'keuskupan_id' => $keuskupanBogor->id
                    ],
                    [
                        'nama' => $data['nama'],
                        'kode' => $kode,
                        'lokasi' => $data['lokasi'],
                        'alamat_lengkap' => $data['alamat_lengkap'],
                        'telepon' => $data['telepon'],
                        'email' => $data['email'],
                        'pastor' => $data['pastor'],
                        'jumlah_umat' => $data['jumlah_umat'],
                        'keuskupan_id' => $keuskupanBogor->id,
                        'is_active' => true,
                    ]
                );
            }
            
            $this->command->info('✅ 8 Gereja untuk Keuskupan Bogor berhasil dibuat');
        }
        
        // ============================================
        // 3. BUAT CONTOH GEREJA UNTUK KEUSKUPAN LAIN
        // ============================================
        $otherKeuskupans = Keuskupan::where('code', '!=', 'BGR')->get();
        
        $exampleChurches = [
            'AGJ' => [
                'nama' => 'Gereja Katedral Jakarta',
                'lokasi' => 'Jl. Katedral No. 1, Jakarta Pusat',
                'alamat_lengkap' => 'Gereja Katedral Jakarta, Jl. Katedral No. 1, Jakarta Pusat',
                'pastor' => 'RD. Andreas Pr',
                'jumlah_umat' => 8000,
            ],
            'BDG' => [
                'nama' => 'Gereja Katedral Bandung',
                'lokasi' => 'Jl. Merdeka No. 10, Bandung',
                'alamat_lengkap' => 'Gereja Katedral Bandung, Jl. Merdeka No. 10, Bandung',
                'pastor' => 'RD. Bambang Pr',
                'jumlah_umat' => 6000,
            ],
            'SMG' => [
                'nama' => 'Gereja Katedral Semarang',
                'lokasi' => 'Jl. Pandanaran No. 12, Semarang',
                'alamat_lengkap' => 'Gereja Katedral Semarang, Jl. Pandanaran No. 12, Semarang',
                'pastor' => 'RD. Cipto Pr',
                'jumlah_umat' => 5500,
            ],
            'MDN' => [
                'nama' => 'Gereja Katedral Medan',
                'lokasi' => 'Jl. Pemuda No. 25, Medan',
                'alamat_lengkap' => 'Gereja Katedral Medan, Jl. Pemuda No. 25, Medan',
                'pastor' => 'RD. Dedi Pr',
                'jumlah_umat' => 4500,
            ],
        ];
        
        foreach ($otherKeuskupans as $keuskupan) {
            if (isset($exampleChurches[$keuskupan->code])) {
                $church = $exampleChurches[$keuskupan->code];
                $kode = $keuskupan->code . '-001';
                
                Gereja::updateOrCreate(
                    [
                        'kode' => $kode,
                        'keuskupan_id' => $keuskupan->id
                    ],
                    [
                        'nama' => $church['nama'],
                        'kode' => $kode,
                        'lokasi' => $church['lokasi'],
                        'alamat_lengkap' => $church['alamat_lengkap'],
                        'telepon' => $keuskupan->phone,
                        'email' => strtolower(str_replace(' ', '', $church['nama'])) . '@' . strtolower($keuskupan->code) . '.org',
                        'pastor' => $church['pastor'],
                        'jumlah_umat' => $church['jumlah_umat'],
                        'keuskupan_id' => $keuskupan->id,
                        'is_active' => true,
                    ]
                );
            }
        }
        
        $this->command->info('✅ Contoh gereja untuk keuskupan lain berhasil dibuat');
        $this->command->info('========================================');
        $this->command->info('✅ SEEDER KEUSKUPAN DAN GEREJA SELESAI');
        
        // Tampilkan ringkasan
        $this->command->newLine();
        $this->command->info('📊 RINGKASAN:');
        $this->command->info('   • Total Keuskupan: ' . Keuskupan::count());
        $this->command->info('   • Total Gereja: ' . Gereja::count());
        $this->command->info('   • Gereja di Bogor: ' . Gereja::where('keuskupan_id', $keuskupanBogor->id)->count() . ' gereja');
    }
}