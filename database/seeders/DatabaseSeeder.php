<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Center;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Schedule;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Vital;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'Abdurahman',
            'email' => 'admin@gmail.com',
            'password' => '091091',
        ]);

        $centers = Center::factory(5)->create();

        // Create users for each center
        $centers->each(function ($center) {
            User::factory(3)->create(['center_id' => $center->id]);
            Doctor::factory(2)->create(['center_id' => $center->id]);
            Schedule::factory(4)->create(['center_id' => $center->id]);

            // Patients with vitals
            Patient::factory(5)
                ->create(['center_id' => $center->id])
                ->each(function ($patient) {
                    Vital::factory(3)->create(['patient_id' => $patient->id]);
                });
        });

        // Create appointments
        Appointment::factory(10)->create();

        // Create products and suppliers
        Product::factory(10)->create();
        Supplier::factory(5)->create();
    }
}
