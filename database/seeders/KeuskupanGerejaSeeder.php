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
                'address' => 'Jl. Kapten Muslihat No. 1, Bogor',
                'phone' => '(0251) 8324567',
                'email' => 'admin@keuskupanbogor.org',
                'is_active' => true,
            ],
            [
                'code' => 'AGJ',
                'name' => 'Keuskupan Agung Jakarta',
                'address' => 'Jl. Katedral No. 1, Jakarta Pusat',
                'phone' => '(021) 3456789',
                'email' => 'admin@keuskupanagungjakarta.org',
                'is_active' => true,
            ],
            [
                'code' => 'BDG',
                'name' => 'Keuskupan Bandung',
                'address' => 'Jl. Merdeka No. 10, Bandung',
                'phone' => '(022) 4567890',
                'email' => 'admin@keuskupanbandung.org',
                'is_active' => true,
            ],
            [
                'code' => 'SMG',
                'name' => 'Keuskupan Agung Semarang',
                'address' => 'Jl. Pandanaran No. 12, Semarang',
                'phone' => '(024) 5678901',
                'email' => 'admin@keuskupanagungsemarang.org',
                'is_active' => true,
            ],
            [
                'code' => 'MDN',
                'name' => 'Keuskupan Agung Medan',
                'address' => 'Jl. Pemuda No. 25, Medan',
                'phone' => '(061) 6789012',
                'email' => 'admin@keuskupanagungmedan.org',
                'is_active' => true,
            ],
            [
                'code' => 'PNG',
                'name' => 'Keuskupan Pangkal Pinang',
                'address' => 'Jl. Jenderal Sudirman No. 123, Pangkal Pinang',
                'phone' => '(0717) 1234567',
                'email' => 'admin@keuskupanpangkalpinang.org',
                'is_active' => true,
            ],
        ];
        
        foreach ($keuskupans as $data) {
            Keuskupan::updateOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
        
        $this->command->info('✅ ' . count($keuskupans) . ' Keuskupan berhasil dibuat');
        
        // ============================================
        // 2. AMBIL KEUSKUPAN BOGOR
        // ============================================
        $keuskupanBogor = Keuskupan::where('code', 'BGR')->first();
        
        // ============================================
        // 3. BUAT GEREJA UNTUK KEUSKUPAN BOGOR (8 GEREJA)
        // ============================================
        if ($keuskupanBogor) {
            $gerejaBogor = [
                [
                    'nama' => 'Gereja Katedral Bogor (Santa Perawan Maria)',
                    'kode' => 'BGR-001',
                    'lokasi' => 'Jl. Kapten Muslihat',
                    'alamat' => 'Gereja Santa Perawan Maria, Jl. Kapten Muslihat No. 1, Bogor',
                    'telepon' => '(0251) 8324567',
                    'email' => 'katedral@keuskupanbogor.org',
                ],
                [
                    'nama' => 'Gereja St. Fransiskus dari Asisi',
                    'kode' => 'BGR-002',
                    'lokasi' => 'Sukasari',
                    'alamat' => 'Gereja St. Fransiskus dari Asisi, Jl. Sukasari Raya No. 45, Bogor',
                    'telepon' => '(0251) 8345678',
                    'email' => 'fransiskus@keuskupanbogor.org',
                ],
                [
                    'nama' => 'Gereja St. Joannes Baptista',
                    'kode' => 'BGR-003',
                    'lokasi' => 'Parung',
                    'alamat' => 'Gereja St. Joannes Baptista, Jl. Raya Parung No. 123, Bogor',
                    'telepon' => '(0251) 8456789',
                    'email' => 'joannes@keuskupanbogor.org',
                ],
                [
                    'nama' => 'Gereja St. Yakobus Rasul',
                    'kode' => 'BGR-004',
                    'lokasi' => 'Megamendung',
                    'alamat' => 'Gereja St. Yakobus Rasul, Jl. Raya Megamendung No. 67, Bogor',
                    'telepon' => '(0251) 8567890',
                    'email' => 'yakobus@keuskupanbogor.org',
                ],
                [
                    'nama' => 'Gereja St. Maria Fatima',
                    'kode' => 'BGR-005',
                    'lokasi' => 'Sentul City',
                    'alamat' => 'Gereja St. Maria Fatima, Sentul City Boulevard, Bogor',
                    'telepon' => '(0251) 8678901',
                    'email' => 'fatima@keuskupanbogor.org',
                ],
                [
                    'nama' => 'Gereja St. Ignatius Loyola',
                    'kode' => 'BGR-006',
                    'lokasi' => 'Semplak',
                    'alamat' => 'Gereja St. Ignatius Loyola, Jl. Semplak No. 89, Bogor',
                    'telepon' => '(0251) 8789012',
                    'email' => 'ignatius@keuskupanbogor.org',
                ],
                [
                    'nama' => 'Gereja Sta. Faustina Kowalska',
                    'kode' => 'BGR-007',
                    'lokasi' => 'Bojong Gede',
                    'alamat' => 'Gereja Sta. Faustina Kowalska, Jl. Raya Bojong Gede No. 234, Bogor',
                    'telepon' => '(0251) 8890123',
                    'email' => 'faustina@keuskupanbogor.org',
                ],
                [
                    'nama' => 'Gereja St. Andreas',
                    'kode' => 'BGR-008',
                    'lokasi' => 'Sukaraja',
                    'alamat' => 'Gereja St. Andreas, Jl. Sukaraja No. 56, Bogor',
                    'telepon' => '(0251) 8901234',
                    'email' => 'andreas@keuskupanbogor.org',
                ],
            ];
            
            foreach ($gerejaBogor as $data) {
                Gereja::updateOrCreate(
                    [
                        'kode' => $data['kode'],
                        'keuskupan_id' => $keuskupanBogor->id
                    ],
                    [
                        'nama' => $data['nama'],
                        'kode' => $data['kode'],
                        'lokasi' => $data['lokasi'],
                        'alamat' => $data['alamat'],
                        'telepon' => $data['telepon'],
                        'email' => $data['email'],
                        'keuskupan_id' => $keuskupanBogor->id,
                        'is_active' => true,
                    ]
                );
            }
            
            $this->command->info('✅ 8 Gereja untuk Keuskupan Bogor berhasil dibuat');
        }
        
        // ============================================
        // 4. BUAT CONTOH GEREJA UNTUK KEUSKUPAN LAIN
        // ============================================
        $otherKeuskupans = Keuskupan::where('code', '!=', 'BGR')->get();
        
        $exampleChurches = [
            'AGJ' => [
                'nama' => 'Gereja Katedral Jakarta',
                'kode' => 'AGJ-001',
                'lokasi' => 'Jakarta Pusat',
                'alamat' => 'Gereja Katedral Jakarta, Jl. Katedral No. 1, Jakarta Pusat',
                'telepon' => '(021) 3456789',
                'email' => 'katedral@keuskupanjakarta.org',
            ],
            'BDG' => [
                'nama' => 'Gereja Katedral Bandung',
                'kode' => 'BDG-001',
                'lokasi' => 'Bandung',
                'alamat' => 'Gereja Katedral Bandung, Jl. Merdeka No. 10, Bandung',
                'telepon' => '(022) 4567890',
                'email' => 'katedral@keuskupanbandung.org',
            ],
            'SMG' => [
                'nama' => 'Gereja Katedral Semarang',
                'kode' => 'SMG-001',
                'lokasi' => 'Semarang',
                'alamat' => 'Gereja Katedral Semarang, Jl. Pandanaran No. 12, Semarang',
                'telepon' => '(024) 5678901',
                'email' => 'katedral@keuskupansemarang.org',
            ],
            'MDN' => [
                'nama' => 'Gereja Katedral Medan',
                'kode' => 'MDN-001',
                'lokasi' => 'Medan',
                'alamat' => 'Gereja Katedral Medan, Jl. Pemuda No. 25, Medan',
                'telepon' => '(061) 6789012',
                'email' => 'katedral@keuskupanmedan.org',
            ],
            'PNG' => [
                'nama' => 'Gereja St. Yoseph',
                'kode' => 'PNG-001',
                'lokasi' => 'Pangkal Pinang',
                'alamat' => 'Gereja St. Yoseph, Jl. Jenderal Sudirman No. 123, Pangkal Pinang',
                'telepon' => '(0717) 1234567',
                'email' => 'yoseph@keuskupanpangkalpinang.org',
            ],
        ];
        
        foreach ($otherKeuskupans as $keuskupan) {
            if (isset($exampleChurches[$keuskupan->code])) {
                $church = $exampleChurches[$keuskupan->code];
                
                Gereja::updateOrCreate(
                    [
                        'kode' => $church['kode'],
                        'keuskupan_id' => $keuskupan->id
                    ],
                    [
                        'nama' => $church['nama'],
                        'kode' => $church['kode'],
                        'lokasi' => $church['lokasi'],
                        'alamat' => $church['alamat'],
                        'telepon' => $church['telepon'],
                        'email' => $church['email'],
                        'keuskupan_id' => $keuskupan->id,
                        'is_active' => true,
                    ]
                );
                
                $this->command->info("✅ Gereja untuk {$keuskupan->name} berhasil dibuat");
            }
        }
        
        // ============================================
        // 5. RINGKASAN
        // ============================================
        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info('📊 RINGKASAN');
        $this->command->info('========================================');
        $this->command->info('   • Total Keuskupan: ' . Keuskupan::count());
        $this->command->info('   • Total Gereja: ' . Gereja::count());
        
        if ($keuskupanBogor) {
            $this->command->info('   • Gereja di Bogor: ' . Gereja::where('keuskupan_id', $keuskupanBogor->id)->count() . ' gereja');
        }
        
        $this->command->newLine();
        $this->command->info('✅ SEEDER KEUSKUPAN DAN GEREJA SELESAI');
    }
}