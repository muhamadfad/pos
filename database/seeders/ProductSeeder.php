<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Buat kategori bahan bangunan
        $categories = [
            'Semen & Perekat',
            'Besi & Baja',
            'Cat & Pelapis',
            'Kayu & Papan',
            'Keramik & Granit',
            'Pipa & Sanitasi',
            'Atap & Genteng',
            'Listrik & Kabel',
            'Alat Pertukangan',
            'Pasir & Batu',
            'Pintu & Jendela',
            'Waterproofing',
        ];

        $categoryMap = [];
        foreach ($categories as $cat) {
            $categoryMap[$cat] = Category::firstOrCreate(['name' => $cat])->id;
        }

        // 100 produk bahan bangunan
        $products = [
            // === Semen & Perekat ===
            ['barcode' => '8991001100011', 'name' => 'Semen Tiga Roda 50kg', 'category' => 'Semen & Perekat', 'price' => 72000, 'purchase_price' => 62000, 'stock' => 200],
            ['barcode' => '8991001100028', 'name' => 'Semen Gresik 50kg', 'category' => 'Semen & Perekat', 'price' => 68000, 'purchase_price' => 58000, 'stock' => 180],
            ['barcode' => '8991001100035', 'name' => 'Semen Padang 50kg', 'category' => 'Semen & Perekat', 'price' => 65000, 'purchase_price' => 55000, 'stock' => 150],
            ['barcode' => '8991001100042', 'name' => 'Semen Holcim 50kg', 'category' => 'Semen & Perekat', 'price' => 70000, 'purchase_price' => 60000, 'stock' => 160],
            ['barcode' => '8991001100059', 'name' => 'Semen Putih Tiga Roda 40kg', 'category' => 'Semen & Perekat', 'price' => 125000, 'purchase_price' => 105000, 'stock' => 80],
            ['barcode' => '8991001100066', 'name' => 'Lem Semen MU-380 20kg', 'category' => 'Semen & Perekat', 'price' => 85000, 'purchase_price' => 70000, 'stock' => 100],
            ['barcode' => '8991001100073', 'name' => 'Semen Instan Mortar Utama 40kg', 'category' => 'Semen & Perekat', 'price' => 55000, 'purchase_price' => 45000, 'stock' => 120],
            ['barcode' => '8991001100080', 'name' => 'Semen Warna Yiyitan 5kg', 'category' => 'Semen & Perekat', 'price' => 35000, 'purchase_price' => 25000, 'stock' => 90],

            // === Besi & Baja ===
            ['barcode' => '8991002200011', 'name' => 'Besi Beton Polos 8mm 12m', 'category' => 'Besi & Baja', 'price' => 52000, 'purchase_price' => 43000, 'stock' => 300],
            ['barcode' => '8991002200028', 'name' => 'Besi Beton Polos 10mm 12m', 'category' => 'Besi & Baja', 'price' => 78000, 'purchase_price' => 65000, 'stock' => 250],
            ['barcode' => '8991002200035', 'name' => 'Besi Beton Polos 12mm 12m', 'category' => 'Besi & Baja', 'price' => 115000, 'purchase_price' => 95000, 'stock' => 200],
            ['barcode' => '8991002200042', 'name' => 'Besi Beton Ulir 10mm 12m', 'category' => 'Besi & Baja', 'price' => 85000, 'purchase_price' => 72000, 'stock' => 180],
            ['barcode' => '8991002200059', 'name' => 'Besi Beton Ulir 13mm 12m', 'category' => 'Besi & Baja', 'price' => 145000, 'purchase_price' => 120000, 'stock' => 150],
            ['barcode' => '8991002200066', 'name' => 'Besi Hollow 40x40 0.8mm', 'category' => 'Besi & Baja', 'price' => 65000, 'purchase_price' => 52000, 'stock' => 120],
            ['barcode' => '8991002200073', 'name' => 'Besi Hollow 20x40 0.8mm', 'category' => 'Besi & Baja', 'price' => 48000, 'purchase_price' => 38000, 'stock' => 150],
            ['barcode' => '8991002200080', 'name' => 'Kawat Bendrat 1kg', 'category' => 'Besi & Baja', 'price' => 18000, 'purchase_price' => 14000, 'stock' => 200],
            ['barcode' => '8991002200097', 'name' => 'Paku 2 inch 1kg', 'category' => 'Besi & Baja', 'price' => 22000, 'purchase_price' => 17000, 'stock' => 300],
            ['barcode' => '8991002200103', 'name' => 'Paku 3 inch 1kg', 'category' => 'Besi & Baja', 'price' => 22000, 'purchase_price' => 17000, 'stock' => 300],
            ['barcode' => '8991002200110', 'name' => 'Wiremesh M5 2.1x5.4m', 'category' => 'Besi & Baja', 'price' => 145000, 'purchase_price' => 120000, 'stock' => 50],

            // === Cat & Pelapis ===
            ['barcode' => '8991003300011', 'name' => 'Cat Dulux Catylac Interior 5kg Putih', 'category' => 'Cat & Pelapis', 'price' => 95000, 'purchase_price' => 78000, 'stock' => 80],
            ['barcode' => '8991003300028', 'name' => 'Cat Dulux Catylac Interior 25kg Putih', 'category' => 'Cat & Pelapis', 'price' => 380000, 'purchase_price' => 310000, 'stock' => 40],
            ['barcode' => '8991003300035', 'name' => 'Cat Dulux Weathershield 2.5L', 'category' => 'Cat & Pelapis', 'price' => 185000, 'purchase_price' => 150000, 'stock' => 60],
            ['barcode' => '8991003300042', 'name' => 'Cat Nippon Vinilex 5kg Putih', 'category' => 'Cat & Pelapis', 'price' => 85000, 'purchase_price' => 68000, 'stock' => 70],
            ['barcode' => '8991003300059', 'name' => 'Cat Nippon Vinilex 25kg Putih', 'category' => 'Cat & Pelapis', 'price' => 340000, 'purchase_price' => 280000, 'stock' => 35],
            ['barcode' => '8991003300066', 'name' => 'Cat Avian Exterior 5kg', 'category' => 'Cat & Pelapis', 'price' => 75000, 'purchase_price' => 60000, 'stock' => 90],
            ['barcode' => '8991003300073', 'name' => 'Cat Minyak Kuda Terbang 1L', 'category' => 'Cat & Pelapis', 'price' => 45000, 'purchase_price' => 35000, 'stock' => 60],
            ['barcode' => '8991003300080', 'name' => 'Plamir Tembok Matex 5kg', 'category' => 'Cat & Pelapis', 'price' => 55000, 'purchase_price' => 42000, 'stock' => 80],
            ['barcode' => '8991003300097', 'name' => 'Thinner A 1L', 'category' => 'Cat & Pelapis', 'price' => 28000, 'purchase_price' => 21000, 'stock' => 120],
            ['barcode' => '8991003300103', 'name' => 'Kuas Cat 4 inch', 'category' => 'Cat & Pelapis', 'price' => 15000, 'purchase_price' => 10000, 'stock' => 150],

            // === Kayu & Papan ===
            ['barcode' => '8991004400011', 'name' => 'Triplek 3mm 122x244cm', 'category' => 'Kayu & Papan', 'price' => 65000, 'purchase_price' => 52000, 'stock' => 100],
            ['barcode' => '8991004400028', 'name' => 'Triplek 9mm 122x244cm', 'category' => 'Kayu & Papan', 'price' => 135000, 'purchase_price' => 110000, 'stock' => 80],
            ['barcode' => '8991004400035', 'name' => 'Triplek 12mm 122x244cm', 'category' => 'Kayu & Papan', 'price' => 175000, 'purchase_price' => 145000, 'stock' => 60],
            ['barcode' => '8991004400042', 'name' => 'Triplek 18mm 122x244cm', 'category' => 'Kayu & Papan', 'price' => 265000, 'purchase_price' => 220000, 'stock' => 40],
            ['barcode' => '8991004400059', 'name' => 'GRC Board 8mm 122x244cm', 'category' => 'Kayu & Papan', 'price' => 85000, 'purchase_price' => 68000, 'stock' => 70],
            ['barcode' => '8991004400066', 'name' => 'Kalsiboard 6mm 122x244cm', 'category' => 'Kayu & Papan', 'price' => 75000, 'purchase_price' => 60000, 'stock' => 60],
            ['barcode' => '8991004400073', 'name' => 'Gypsum Jayaboard 9mm 122x244cm', 'category' => 'Kayu & Papan', 'price' => 72000, 'purchase_price' => 58000, 'stock' => 90],
            ['barcode' => '8991004400080', 'name' => 'Hollow Galvalum 2x4 0.3mm', 'category' => 'Kayu & Papan', 'price' => 28000, 'purchase_price' => 22000, 'stock' => 200],

            // === Keramik & Granit ===
            ['barcode' => '8991005500011', 'name' => 'Keramik Lantai 40x40 Putih Polos', 'category' => 'Keramik & Granit', 'price' => 48000, 'purchase_price' => 38000, 'stock' => 500],
            ['barcode' => '8991005500028', 'name' => 'Keramik Lantai 60x60 Cream Polos', 'category' => 'Keramik & Granit', 'price' => 65000, 'purchase_price' => 52000, 'stock' => 400],
            ['barcode' => '8991005500035', 'name' => 'Keramik Dinding 25x40 Putih', 'category' => 'Keramik & Granit', 'price' => 55000, 'purchase_price' => 43000, 'stock' => 350],
            ['barcode' => '8991005500042', 'name' => 'Keramik Kamar Mandi 20x25 Biru', 'category' => 'Keramik & Granit', 'price' => 52000, 'purchase_price' => 40000, 'stock' => 300],
            ['barcode' => '8991005500059', 'name' => 'Granit Lantai 60x60 Hitam', 'category' => 'Keramik & Granit', 'price' => 95000, 'purchase_price' => 78000, 'stock' => 200],
            ['barcode' => '8991005500066', 'name' => 'Granit Lantai 60x60 Cream Marmer', 'category' => 'Keramik & Granit', 'price' => 105000, 'purchase_price' => 85000, 'stock' => 180],
            ['barcode' => '8991005500073', 'name' => 'Keramik Tangga 30x60 Motif Kayu', 'category' => 'Keramik & Granit', 'price' => 75000, 'purchase_price' => 60000, 'stock' => 150],
            ['barcode' => '8991005500080', 'name' => 'Nat Keramik Sika 1kg Putih', 'category' => 'Keramik & Granit', 'price' => 12000, 'purchase_price' => 8000, 'stock' => 400],

            // === Pipa & Sanitasi ===
            ['barcode' => '8991006600011', 'name' => 'Pipa PVC Wavin D 3/4" 4m', 'category' => 'Pipa & Sanitasi', 'price' => 32000, 'purchase_price' => 25000, 'stock' => 200],
            ['barcode' => '8991006600028', 'name' => 'Pipa PVC Wavin D 1" 4m', 'category' => 'Pipa & Sanitasi', 'price' => 42000, 'purchase_price' => 33000, 'stock' => 180],
            ['barcode' => '8991006600035', 'name' => 'Pipa PVC Wavin D 2" 4m', 'category' => 'Pipa & Sanitasi', 'price' => 55000, 'purchase_price' => 43000, 'stock' => 150],
            ['barcode' => '8991006600042', 'name' => 'Pipa PVC Wavin D 3" 4m', 'category' => 'Pipa & Sanitasi', 'price' => 85000, 'purchase_price' => 68000, 'stock' => 100],
            ['barcode' => '8991006600059', 'name' => 'Pipa PVC Wavin D 4" 4m', 'category' => 'Pipa & Sanitasi', 'price' => 115000, 'purchase_price' => 92000, 'stock' => 80],
            ['barcode' => '8991006600066', 'name' => 'Kran Air Tembok 1/2"', 'category' => 'Pipa & Sanitasi', 'price' => 25000, 'purchase_price' => 18000, 'stock' => 120],
            ['barcode' => '8991006600073', 'name' => 'Stop Kran 1/2" Kuningan', 'category' => 'Pipa & Sanitasi', 'price' => 35000, 'purchase_price' => 25000, 'stock' => 100],
            ['barcode' => '8991006600080', 'name' => 'Closet Duduk TOTO CW421J', 'category' => 'Pipa & Sanitasi', 'price' => 1850000, 'purchase_price' => 1550000, 'stock' => 15],
            ['barcode' => '8991006600097', 'name' => 'Closet Jongkok Ina', 'category' => 'Pipa & Sanitasi', 'price' => 85000, 'purchase_price' => 65000, 'stock' => 30],
            ['barcode' => '8991006600103', 'name' => 'Wastafel TOTO LW241CJ', 'category' => 'Pipa & Sanitasi', 'price' => 750000, 'purchase_price' => 620000, 'stock' => 10],
            ['barcode' => '8991006600110', 'name' => 'Lem Pipa PVC Isarplas 50g', 'category' => 'Pipa & Sanitasi', 'price' => 8000, 'purchase_price' => 5000, 'stock' => 250],

            // === Atap & Genteng ===
            ['barcode' => '8991007700011', 'name' => 'Genteng Metal Rainbow', 'category' => 'Atap & Genteng', 'price' => 38000, 'purchase_price' => 30000, 'stock' => 500],
            ['barcode' => '8991007700028', 'name' => 'Genteng Keramik KIA', 'category' => 'Atap & Genteng', 'price' => 12000, 'purchase_price' => 9000, 'stock' => 1000],
            ['barcode' => '8991007700035', 'name' => 'Genteng Beton Flat', 'category' => 'Atap & Genteng', 'price' => 8500, 'purchase_price' => 6500, 'stock' => 800],
            ['barcode' => '8991007700042', 'name' => 'Atap Spandek 0.3mm 6m', 'category' => 'Atap & Genteng', 'price' => 85000, 'purchase_price' => 68000, 'stock' => 200],
            ['barcode' => '8991007700059', 'name' => 'Atap Spandek 0.4mm 6m', 'category' => 'Atap & Genteng', 'price' => 110000, 'purchase_price' => 90000, 'stock' => 150],
            ['barcode' => '8991007700066', 'name' => 'Atap Galvalum 0.35mm 6m', 'category' => 'Atap & Genteng', 'price' => 95000, 'purchase_price' => 78000, 'stock' => 180],
            ['barcode' => '8991007700073', 'name' => 'Atap Asbes 3m', 'category' => 'Atap & Genteng', 'price' => 55000, 'purchase_price' => 43000, 'stock' => 100],
            ['barcode' => '8991007700080', 'name' => 'Nok Genteng Metal', 'category' => 'Atap & Genteng', 'price' => 45000, 'purchase_price' => 35000, 'stock' => 200],
            ['barcode' => '8991007700097', 'name' => 'Talang PVC 4m', 'category' => 'Atap & Genteng', 'price' => 65000, 'purchase_price' => 50000, 'stock' => 80],

            // === Listrik & Kabel ===
            ['barcode' => '8991008800011', 'name' => 'Kabel NYM 2x1.5mm 50m Eterna', 'category' => 'Listrik & Kabel', 'price' => 285000, 'purchase_price' => 235000, 'stock' => 50],
            ['barcode' => '8991008800028', 'name' => 'Kabel NYM 2x2.5mm 50m Eterna', 'category' => 'Listrik & Kabel', 'price' => 435000, 'purchase_price' => 360000, 'stock' => 40],
            ['barcode' => '8991008800035', 'name' => 'Kabel NYA 1x1.5mm 50m', 'category' => 'Listrik & Kabel', 'price' => 125000, 'purchase_price' => 100000, 'stock' => 60],
            ['barcode' => '8991008800042', 'name' => 'Kabel NYA 1x2.5mm 50m', 'category' => 'Listrik & Kabel', 'price' => 195000, 'purchase_price' => 160000, 'stock' => 50],
            ['barcode' => '8991008800059', 'name' => 'Saklar Tunggal Panasonic', 'category' => 'Listrik & Kabel', 'price' => 25000, 'purchase_price' => 18000, 'stock' => 150],
            ['barcode' => '8991008800066', 'name' => 'Saklar Ganda Panasonic', 'category' => 'Listrik & Kabel', 'price' => 35000, 'purchase_price' => 25000, 'stock' => 120],
            ['barcode' => '8991008800073', 'name' => 'Stop Kontak Panasonic', 'category' => 'Listrik & Kabel', 'price' => 28000, 'purchase_price' => 20000, 'stock' => 130],
            ['barcode' => '8991008800080', 'name' => 'MCB 1P 6A Schneider', 'category' => 'Listrik & Kabel', 'price' => 45000, 'purchase_price' => 35000, 'stock' => 80],
            ['barcode' => '8991008800097', 'name' => 'Lampu LED Philips 12W', 'category' => 'Listrik & Kabel', 'price' => 32000, 'purchase_price' => 24000, 'stock' => 200],
            ['barcode' => '8991008800103', 'name' => 'Fitting Lampu E27', 'category' => 'Listrik & Kabel', 'price' => 8000, 'purchase_price' => 5000, 'stock' => 300],

            // === Alat Pertukangan ===
            ['barcode' => '8991009900011', 'name' => 'Palu Kambing 1.5lb Tekiro', 'category' => 'Alat Pertukangan', 'price' => 55000, 'purchase_price' => 42000, 'stock' => 40],
            ['barcode' => '8991009900028', 'name' => 'Gergaji Kayu 18"', 'category' => 'Alat Pertukangan', 'price' => 45000, 'purchase_price' => 33000, 'stock' => 35],
            ['barcode' => '8991009900035', 'name' => 'Meteran 5m Stanley', 'category' => 'Alat Pertukangan', 'price' => 65000, 'purchase_price' => 50000, 'stock' => 50],
            ['barcode' => '8991009900042', 'name' => 'Waterpas 60cm', 'category' => 'Alat Pertukangan', 'price' => 75000, 'purchase_price' => 58000, 'stock' => 30],
            ['barcode' => '8991009900059', 'name' => 'Sendok Semen', 'category' => 'Alat Pertukangan', 'price' => 18000, 'purchase_price' => 12000, 'stock' => 100],
            ['barcode' => '8991009900066', 'name' => 'Sekop Pasir', 'category' => 'Alat Pertukangan', 'price' => 45000, 'purchase_price' => 33000, 'stock' => 60],
            ['barcode' => '8991009900073', 'name' => 'Cangkul Gagang Kayu', 'category' => 'Alat Pertukangan', 'price' => 55000, 'purchase_price' => 40000, 'stock' => 40],
            ['barcode' => '8991009900080', 'name' => 'Ember Semen 20L', 'category' => 'Alat Pertukangan', 'price' => 15000, 'purchase_price' => 10000, 'stock' => 200],

            // === Pasir & Batu ===
            ['barcode' => '8991010100011', 'name' => 'Pasir Cor per Kubik', 'category' => 'Pasir & Batu', 'price' => 350000, 'purchase_price' => 280000, 'stock' => 50],
            ['barcode' => '8991010100028', 'name' => 'Pasir Pasang per Kubik', 'category' => 'Pasir & Batu', 'price' => 280000, 'purchase_price' => 220000, 'stock' => 50],
            ['barcode' => '8991010100035', 'name' => 'Split/Kerikil per Kubik', 'category' => 'Pasir & Batu', 'price' => 320000, 'purchase_price' => 260000, 'stock' => 40],
            ['barcode' => '8991010100042', 'name' => 'Batu Bata Merah per 1000', 'category' => 'Pasir & Batu', 'price' => 650000, 'purchase_price' => 520000, 'stock' => 30],
            ['barcode' => '8991010100059', 'name' => 'Batako Press per Buah', 'category' => 'Pasir & Batu', 'price' => 3500, 'purchase_price' => 2500, 'stock' => 2000],
            ['barcode' => '8991010100066', 'name' => 'Bata Ringan Hebel 60x20x10cm', 'category' => 'Pasir & Batu', 'price' => 8500, 'purchase_price' => 6800, 'stock' => 1500],
            ['barcode' => '8991010100073', 'name' => 'Paving Block 6cm Abu-Abu', 'category' => 'Pasir & Batu', 'price' => 1200, 'purchase_price' => 850, 'stock' => 5000],
            ['barcode' => '8991010100080', 'name' => 'Paving Block 8cm Merah', 'category' => 'Pasir & Batu', 'price' => 1800, 'purchase_price' => 1300, 'stock' => 3000],

            // === Pintu & Jendela ===
            ['barcode' => '8991011100011', 'name' => 'Pintu Panel Kayu Meranti', 'category' => 'Pintu & Jendela', 'price' => 450000, 'purchase_price' => 360000, 'stock' => 20],
            ['barcode' => '8991011100028', 'name' => 'Pintu PVC Kamar Mandi', 'category' => 'Pintu & Jendela', 'price' => 185000, 'purchase_price' => 145000, 'stock' => 25],
            ['barcode' => '8991011100035', 'name' => 'Pintu Besi Hollow Minimalis', 'category' => 'Pintu & Jendela', 'price' => 850000, 'purchase_price' => 680000, 'stock' => 10],
            ['barcode' => '8991011100042', 'name' => 'Kusen Aluminium 4" per Meter', 'category' => 'Pintu & Jendela', 'price' => 65000, 'purchase_price' => 50000, 'stock' => 100],
            ['barcode' => '8991011100059', 'name' => 'Jendela Aluminium 60x120cm', 'category' => 'Pintu & Jendela', 'price' => 350000, 'purchase_price' => 280000, 'stock' => 15],
            ['barcode' => '8991011100066', 'name' => 'Engsel Pintu 4" Stainless', 'category' => 'Pintu & Jendela', 'price' => 25000, 'purchase_price' => 18000, 'stock' => 200],
            ['barcode' => '8991011100073', 'name' => 'Handle Pintu Minimalis', 'category' => 'Pintu & Jendela', 'price' => 85000, 'purchase_price' => 65000, 'stock' => 50],
            ['barcode' => '8991011100080', 'name' => 'Kunci Pintu Double Lock', 'category' => 'Pintu & Jendela', 'price' => 120000, 'purchase_price' => 95000, 'stock' => 40],

            // === Waterproofing ===
            ['barcode' => '8991012200011', 'name' => 'Waterproofing Aquaproof 4kg', 'category' => 'Waterproofing', 'price' => 145000, 'purchase_price' => 115000, 'stock' => 60],
            ['barcode' => '8991012200028', 'name' => 'Waterproofing Aquaproof 20kg', 'category' => 'Waterproofing', 'price' => 550000, 'purchase_price' => 440000, 'stock' => 25],
            ['barcode' => '8991012200035', 'name' => 'Waterproofing Sika Top 107 25kg', 'category' => 'Waterproofing', 'price' => 380000, 'purchase_price' => 310000, 'stock' => 30],
            ['barcode' => '8991012200042', 'name' => 'Membran Bakar 1x20m', 'category' => 'Waterproofing', 'price' => 650000, 'purchase_price' => 520000, 'stock' => 15],
            ['barcode' => '8991012200059', 'name' => 'Sealant Silikon Wacker 280ml', 'category' => 'Waterproofing', 'price' => 42000, 'purchase_price' => 32000, 'stock' => 100],
            ['barcode' => '8991012200066', 'name' => 'Sealant Polyurethane Sika 600ml', 'category' => 'Waterproofing', 'price' => 75000, 'purchase_price' => 58000, 'stock' => 60],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['barcode' => $product['barcode']],
                [
                    'name' => $product['name'],
                    'category_id' => $categoryMap[$product['category']],
                    'price' => $product['price'],
                    'purchase_price' => $product['purchase_price'],
                    'stock' => $product['stock'],
                ]
            );
        }
    }
}
