<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Settings\Models\Setting;
use App\Shared\Sequences\Sequence;
use Illuminate\Database\Seeder;

class SystemDefaultsSeeder extends Seeder
{
    public function run(): void
    {
        $sequences = [
            ['name' => 'patient', 'prefix' => 'P', 'padding' => 6, 'include_year' => true],
            ['name' => 'appointment', 'prefix' => 'APT', 'padding' => 6, 'include_year' => true],
            ['name' => 'consultation', 'prefix' => 'CON', 'padding' => 6, 'include_year' => true],
            ['name' => 'prescription', 'prefix' => 'RX', 'padding' => 6, 'include_year' => true],
            ['name' => 'invoice', 'prefix' => 'INV', 'padding' => 6, 'include_year' => true],
            ['name' => 'payment', 'prefix' => 'PAY', 'padding' => 6, 'include_year' => true],
            ['name' => 'expense', 'prefix' => 'EXP', 'padding' => 6, 'include_year' => true],
            ['name' => 'admission', 'prefix' => 'ADM', 'padding' => 6, 'include_year' => true],
        ];

        foreach ($sequences as $seq) {
            Sequence::firstOrCreate(
                ['name' => $seq['name']],
                [
                    'prefix' => $seq['prefix'],
                    'current_value' => 0,
                    'padding' => $seq['padding'],
                    'include_year' => $seq['include_year'],
                    'last_year' => (int) now()->utc()->format('Y'),
                ]
            );
        }

        $settings = [
            ['key' => 'hospital.name', 'value' => 'MediCore General Hospital', 'type' => 'string', 'group' => 'general', 'description' => 'Official hospital name shown on invoices and prescriptions'],
            ['key' => 'hospital.currency', 'value' => 'PKR', 'type' => 'string', 'group' => 'billing', 'description' => 'Default billing currency code'],
            ['key' => 'billing.default_tax_rate', 'value' => '0', 'type' => 'float', 'group' => 'billing', 'description' => 'Default tax percentage applied to new invoice items'],
            ['key' => 'appointments.slot_duration_minutes', 'value' => '15', 'type' => 'int', 'group' => 'appointments', 'description' => 'Default consultation slot length in minutes'],
            ['key' => 'appointments.cancel_cutoff_hours', 'value' => '2', 'type' => 'int', 'group' => 'appointments', 'description' => 'Minimum hours before appointment start when patient cancellation is allowed'],
            ['key' => 'appointments.max_active_per_patient', 'value' => '3', 'type' => 'int', 'group' => 'appointments', 'description' => 'Maximum concurrent upcoming appointments allowed per patient'],
        ];

        foreach ($settings as $item) {
            Setting::firstOrCreate(
                ['key' => $item['key']],
                [
                    'value' => $item['value'],
                    'type' => $item['type'],
                    'group' => $item['group'],
                    'description' => $item['description'],
                ]
            );
        }
    }
}
