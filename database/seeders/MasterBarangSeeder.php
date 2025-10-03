<?php
// File: database/seeders/MasterBarangSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterBarang;
use App\Models\User;

class MasterBarangSeeder extends Seeder
{
    public function run(): void
    {
        $adminUmum = User::where('role', 'admin_gudang_umum')->first();
        $adminSparepart = User::where('role', 'admin_gudang_sparepart')->first();

        // Master Barang Kategori UMUM
        $barangUmum = [
            [
                'nama_barang' => 'Kertas A4 80gsm',
                'satuan' => 'rim',
                'deskripsi' => 'Kertas fotokopi ukuran A4 dengan ketebalan 80gsm',
                'stok_minimum' => 10,
                'stok_maksimum' => 100,
                'stok_tersedia' => 45,
                'lokasi_gudang' => 'Gudang Utama - Rak A1',
                'harga_rata_rata' => 35000,
                'supplier_utama' => 'PT Kertas Sejahtera'
            ],
            [
                'nama_barang' => 'Pulpen Biru Standard',
                'satuan' => 'pcs',
                'deskripsi' => 'Pulpen tinta biru untuk keperluan kantor',
                'stok_minimum' => 50,
                'stok_maksimum' => 500,
                'stok_tersedia' => 125,
                'lokasi_gudang' => 'Gudang Utama - Rak B2',
                'harga_rata_rata' => 2500,
                'supplier_utama' => 'CV Alat Tulis Mandiri'
            ],
            [
                'nama_barang' => 'Tinta Printer Canon Black',
                'satuan' => 'botol',
                'deskripsi' => 'Tinta refill printer Canon warna hitam',
                'stok_minimum' => 5,
                'stok_maksimum' => 50,
                'stok_tersedia' => 8,
                'lokasi_gudang' => 'Gudang Utama - Rak C3',
                'harga_rata_rata' => 45000,
                'supplier_utama' => 'Toko Komputer Jaya'
            ],
            [
                'nama_barang' => 'Stapler Besar',
                'satuan' => 'pcs',
                'deskripsi' => 'Stapler ukuran besar untuk dokumen tebal',
                'stok_minimum' => 3,
                'stok_maksimum' => 20,
                'stok_tersedia' => 2, // Stok minimum
                'lokasi_gudang' => 'Gudang Utama - Rak D4',
                'harga_rata_rata' => 85000,
                'supplier_utama' => 'PT Office Equipment'
            ],
            [
                'nama_barang' => 'Map Plastik Warna',
                'satuan' => 'pcs',
                'deskripsi' => 'Map plastik berbagai warna untuk filling dokumen',
                'stok_minimum' => 20,
                'stok_maksimum' => 200,
                'stok_tersedia' => 75,
                'lokasi_gudang' => 'Gudang Utama - Rak A2',
                'harga_rata_rata' => 3500,
                'supplier_utama' => 'CV Alat Tulis Mandiri'
            ],
            [
                'nama_barang' => 'Penghapus Putih',
                'satuan' => 'pcs',
                'deskripsi' => 'Penghapus putih kualitas bagus',
                'stok_minimum' => 10,
                'stok_maksimum' => 100,
                'stok_tersedia' => 0, // Stok habis
                'lokasi_gudang' => 'Gudang Utama - Rak B1',
                'harga_rata_rata' => 1500,
                'supplier_utama' => 'CV Alat Tulis Mandiri'
            ]
        ];

        foreach ($barangUmum as $item) {
            MasterBarang::create(array_merge($item, [
                'kategori' => 'umum',
                'created_by' => $adminUmum?->id
            ]));
        }

        // Master Barang Kategori SPAREPART
        $barangSparepart = [
            [
                'nama_barang' => 'Filter Udara Mitsubishi L200',
                'satuan' => 'pcs',
                'deskripsi' => 'Filter udara untuk kendaraan Mitsubishi L200',
                'stok_minimum' => 2,
                'stok_maksimum' => 20,
                'stok_tersedia' => 5,
                'lokasi_gudang' => 'Gudang Sparepart - Zona A',
                'harga_rata_rata' => 125000,
                'supplier_utama' => 'PT Mitsubishi Parts'
            ],
            [
                'nama_barang' => 'Belt Conveyor 1200mm',
                'satuan' => 'meter',
                'deskripsi' => 'Belt conveyor lebar 1200mm untuk sistem produksi',
                'stok_minimum' => 10,
                'stok_maksimum' => 100,
                'stok_tersedia' => 25,
                'lokasi_gudang' => 'Gudang Sparepart - Zona B',
                'harga_rata_rata' => 85000,
                'supplier_utama' => 'PT Conveyor Indonesia'
            ],
            [
                'nama_barang' => 'Bearing 6204 SKF',
                'satuan' => 'pcs',
                'deskripsi' => 'Bearing tipe 6204 merk SKF original',
                'stok_minimum' => 5,
                'stok_maksimum' => 50,
                'stok_tersedia' => 12,
                'lokasi_gudang' => 'Gudang Sparepart - Zona C',
                'harga_rata_rata' => 95000,
                'supplier_utama' => 'CV Bearing Center'
            ],
            [
                'nama_barang' => 'Oli Mesin SAE 40',
                'satuan' => 'liter',
                'deskripsi' => 'Oli mesin SAE 40 untuk mesin industri',
                'stok_minimum' => 20,
                'stok_maksimum' => 200,
                'stok_tersedia' => 15, // Mendekati minimum
                'lokasi_gudang' => 'Gudang Sparepart - Zona D',
                'harga_rata_rata' => 45000,
                'supplier_utama' => 'PT Pertamina Lubricants'
            ],
            [
                'nama_barang' => 'Seal Hydraulic 50mm',
                'satuan' => 'set',
                'deskripsi' => 'Seal hydraulic diameter 50mm complete set',
                'stok_minimum' => 3,
                'stok_maksimum' => 30,
                'stok_tersedia' => 8,
                'lokasi_gudang' => 'Gudang Sparepart - Zona E',
                'harga_rata_rata' => 275000,
                'supplier_utama' => 'PT Hydraulic Parts'
            ],
            [
                'nama_barang' => 'V-Belt A-75',
                'satuan' => 'pcs',
                'deskripsi' => 'V-Belt ukuran A-75 untuk transmisi mesin',
                'stok_minimum' => 4,
                'stok_maksimum' => 40,
                'stok_tersedia' => 1, // Stok kritis
                'lokasi_gudang' => 'Gudang Sparepart - Zona F',
                'harga_rata_rata' => 65000,
                'supplier_utama' => 'CV Belt & Pulley'
            ],
            [
                'nama_barang' => 'Gear Motor Reducer',
                'satuan' => 'unit',
                'deskripsi' => 'Gear motor reducer ratio 1:50',
                'stok_minimum' => 1,
                'stok_maksimum' => 10,
                'stok_tersedia' => 3,
                'lokasi_gudang' => 'Gudang Sparepart - Zona G',
                'harga_rata_rata' => 2500000,
                'supplier_utama' => 'PT Motor Elektrik'
            ]
        ];

        foreach ($barangSparepart as $item) {
            MasterBarang::create(array_merge($item, [
                'kategori' => 'sparepart',
                'created_by' => $adminSparepart?->id
            ]));
        }

        // Update status stok untuk semua barang yang baru dibuat
        $allBarangs = MasterBarang::all();
        foreach ($allBarangs as $barang) {
            $barang->updateStatusStok();
        }

        echo "✅ Master barang seeded successfully\n";
        echo "   - " . count($barangUmum) . " barang kategori umum\n";
        echo "   - " . count($barangSparepart) . " barang kategori sparepart\n";
    }
}