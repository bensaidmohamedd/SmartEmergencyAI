<?php

namespace Database\Seeders;

use App\Models\EmergencyService;
use Illuminate\Database\Seeder;

class EmergencyServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Caserne Pompiers Plateau', 'type' => 'pompiers', 'phone' => '18', 'address' => 'Avenue de la République, Plateau — Niamey', 'latitude' => 13.5135, 'longitude' => 2.1095, 'zone' => 'Plateau'],
            ['name' => 'Caserne Pompiers Yantala', 'type' => 'pompiers', 'phone' => '18', 'address' => 'Yantala, Niamey', 'latitude' => 13.5280, 'longitude' => 2.1450, 'zone' => 'Yantala'],
            ['name' => 'Caserne Pompiers Lazaret', 'type' => 'pompiers', 'phone' => '18', 'address' => 'Quartier Lazaret, Niamey', 'latitude' => 13.4980, 'longitude' => 2.0850, 'zone' => 'Lazaret'],
            ['name' => 'Commissariat Central Niamey', 'type' => 'police', 'phone' => '17', 'address' => 'Plateau, Niamey', 'latitude' => 13.5120, 'longitude' => 2.1110, 'zone' => 'Plateau'],
            ['name' => 'Commissariat Wadata', 'type' => 'police', 'phone' => '17', 'address' => 'Wadata, Niamey', 'latitude' => 13.5200, 'longitude' => 2.1300, 'zone' => 'Wadata'],
            ['name' => 'SAMU Hôpital National', 'type' => 'samu', 'phone' => '15', 'address' => 'Hôpital National, Niamey', 'latitude' => 13.5150, 'longitude' => 2.1250, 'zone' => 'Centre'],
            ['name' => 'SAMU Lamordé', 'type' => 'samu', 'phone' => '15', 'address' => 'Lamordé, Niamey', 'latitude' => 13.5050, 'longitude' => 2.1600, 'zone' => 'Lamordé'],
            ['name' => 'Gendarmerie Nationale', 'type' => 'gendarmerie', 'phone' => '17', 'address' => 'Route de Say, Niamey', 'latitude' => 13.5100, 'longitude' => 2.0950, 'zone' => 'Niamey'],
        ];

        foreach ($services as $service) {
            EmergencyService::updateOrCreate(
                ['name' => $service['name']],
                $service
            );
        }
    }
}
