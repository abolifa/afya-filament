<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Center;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Schedule;
use App\Models\Supplier;
use App\Models\TransferInvoice;
use App\Models\TransferInvoiceItem;
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

        // 1) Core catalogs
        Product::factory(50)->create();
        Supplier::factory(20)->create();

        Center::factory(5)
            ->has(Doctor::factory(3), 'doctors')
            ->has(Schedule::factory(7), 'schedules')
            ->has(User::factory(2), 'users')
            ->create()
            ->each(function (Center $center) {
                $patients = Patient::factory(20)
                    ->for($center, 'center')
                    ->create();

                foreach ($patients as $patient) {
                    Vital::factory(5)
                        ->for($patient, 'patient')
                        ->create();

                    Appointment::factory(3)
                        ->for($center, 'center')
                        ->for($patient, 'patient')
                        ->create();
                }

                // 5) Create Orders for each patient, each with 1–5 items
                foreach ($patients as $patient) {
                    // decide whether to attach an appointment
                    $attachAppointment = rand(1, 100) <= 70;
                    $orderFactory = Order::factory()
                        ->for($center, 'center')
                        ->for($patient, 'patient');

                    if ($attachAppointment) {
                        // pick one of the patient’s appointments at random
                        $appointment = $patient->appointments()->inRandomOrder()->first();
                        // only if we really got an appointment
                        if ($appointment) {
                            $orderFactory->for($appointment, 'appointment');
                        }
                    }

                    // now always attach 1–5 items
                    $orderFactory
                        ->has(
                            OrderItem::factory(rand(1, 5)),
                            'items'
                        )
                        ->create();
                }


                Supplier::inRandomOrder()
                    ->take(5)
                    ->each(function (Supplier $supplier) use ($center) {
                        Invoice::factory()
                            ->for($center, 'center')
                            ->for($supplier, 'supplier')
                            ->has(
                                InvoiceItem::factory(rand(1, 6)),
                                'items'
                            )
                            ->create();
                    });

                $others = Center::where('id', '!=', $center->id)->get();
                foreach (range(1, 3) as $ignored) {
                    $toCenter = $others->random();
                    TransferInvoice::factory()
                        ->for($center, 'fromCenter')
                        ->for($toCenter, 'toCenter')
                        ->has(
                            TransferInvoiceItem::factory(rand(1, 4)),
                            'items'
                        )
                        ->create();
                }
            });
    }
}
