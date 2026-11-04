<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'code' => 'CAM-001',
                'name' => 'Sony Alpha A7 Mark III (Body Only)',
                'category' => 'Kamera',
                'description' => 'Kamera mirrorless full-frame 24.2 MP, 4K HDR recording untuk produksi video dan fotografi.',
                'total_stock' => 2,
                'available_stock' => 2,
                'condition' => 'baik',
                'location' => 'Lemari Drybox A1',
            ],
            [
                'code' => 'CAM-002',
                'name' => 'Canon EOS 80D + Lensa 18-135mm',
                'category' => 'Kamera',
                'description' => 'Kamera DSLR 24.2 MP Dual Pixel CMOS AF untuk pemula dan praktikum wawancara.',
                'total_stock' => 3,
                'available_stock' => 3,
                'condition' => 'baik',
                'location' => 'Lemari Drybox A2',
            ],
            [
                'code' => 'LNS-001',
                'name' => 'Sony FE 50mm f/1.8 Prime Lens',
                'category' => 'Lensa',
                'description' => 'Lensa portrait bokeh cinematic untuk Sony E-mount.',
                'total_stock' => 2,
                'available_stock' => 2,
                'condition' => 'baik',
                'location' => 'Lemari Drybox B1',
            ],
            [
                'code' => 'MIC-001',
                'name' => 'Saramonic Blink 500 Pro B2 (Wireless Dual Mic)',
                'category' => 'Audio',
                'description' => 'Wireless microphone system 2.4GHz dengan 2 transmitter dan 1 receiver untuk wawancara.',
                'total_stock' => 2,
                'available_stock' => 2,
                'condition' => 'baik',
                'location' => 'Laci Audio C1',
            ],
            [
                'code' => 'MIC-002',
                'name' => 'Boya BY-MM1 Shotgun Mini Microphone',
                'category' => 'Audio',
                'description' => 'Mic on-camera cardioid compact dengan deadcat windshield.',
                'total_stock' => 4,
                'available_stock' => 4,
                'condition' => 'baik',
                'location' => 'Laci Audio C2',
            ],
            [
                'code' => 'LGT-001',
                'name' => 'Godox SL-60W LED Continuous Video Light',
                'category' => 'Lighting',
                'description' => 'Lampu studio daylight 5600K 60W Bowens mount dengan softbox octagonal.',
                'total_stock' => 2,
                'available_stock' => 2,
                'condition' => 'baik',
                'location' => 'Area Stand Lampu D1',
            ],
            [
                'code' => 'TRP-001',
                'name' => 'Tripod Beike Q999H Multifungsi Horizontal',
                'category' => 'Tripod & Grip',
                'description' => 'Tripod heavy duty bisa posisi horizontal overhead dan monopod.',
                'total_stock' => 3,
                'available_stock' => 3,
                'condition' => 'baik',
                'location' => 'Rak Tripod E1',
            ],
            [
                'code' => 'GMB-001',
                'name' => 'DJI Ronin-SC 3-Axis Gimbal Stabilizer',
                'category' => 'Tripod & Grip',
                'description' => 'Gimbal motorized stabilizer untuk kamera mirrorless pergerakan dinamis.',
                'total_stock' => 1,
                'available_stock' => 1,
                'condition' => 'baik',
                'location' => 'Koper Khusus G1',
            ],
        ];

        foreach ($items as $item) {
            Item::updateOrCreate(['code' => $item['code']], $item);
        }
    }
}
