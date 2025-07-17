<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppointment extends EditRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function fillForm(): void
    {
        $record = $this->record;

        $data = $record->attributesToArray();

        $data['has_order'] = $record->order !== null;

        $data['order_items'] = $record->order?->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
            ];
        })->toArray() ?? [];

        $this->form->fill($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['has_order'], $data['order_items']);
        return $data;
    }

    protected function afterSave(): void
    {
        $formData = $this->form->getState();
        $record = $this->record;

        if (!empty($formData['has_order']) && !empty($formData['order_items'])) {
            $order = $record->order;

            if (!$order) {
                $order = Order::create([
                    'status' => 'pending',
                    'center_id' => $record->center_id,
                    'patient_id' => $record->patient_id,
                    'appointment_id' => $record->id,
                ]);
            } else {
                // Optional: update order meta if needed
                $order->update([
                    'center_id' => $record->center_id,
                    'patient_id' => $record->patient_id,
                ]);

                // Delete old items
                $order->items()->delete();
            }

            foreach ($formData['order_items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
        } elseif ($record->order) {
            // If toggle is OFF, delete order and items
            $record->order->items()->delete();
            $record->order->delete();
        }
    }
}
