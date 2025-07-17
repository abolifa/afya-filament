<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Resources\Pages\CreateRecord;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function afterCreate(): void
    {
        $data = $this->form->getState();

        if (!($data['has_order'] ?? false)) {
            return;
        }

        $appointment = $this->record;

        // Create order
        $order = Order::create([
            'status' => 'pending',
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'center_id' => $appointment->center_id,
        ]);

        // Create order items
        foreach ($data['order_items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }
    }
}
