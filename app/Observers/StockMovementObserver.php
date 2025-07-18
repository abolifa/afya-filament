<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\StockMovement;
use App\Models\TransferInvoice;
use Illuminate\Support\Facades\Auth;

class StockMovementObserver
{


    /**
     * Handle the "updated" event on Orders, Invoices, and TransferInvoices.
     */
    public function updated($model): void
    {
        // we only care about status changes
        if (!isset($model->status)) {
            return;
        }

        $from = $model->getOriginal('status');
        $to = $model->status;

        // only run once: pending -> confirmed
        if ($from === 'confirmed' || $to !== 'confirmed') {
            return;
        }

        // determine movement meta and grab items
        if ($model instanceof Invoice) {
            $type = 'in';
            $fromCenter = null;
            $toCenter = $model->center_id;
            $patient = null;
            $supplier = $model->supplier_id;
            $items = $model->items;          // <-- these now exist
        } elseif ($model instanceof Order) {
            $type = 'out';
            $fromCenter = $model->center_id;
            $toCenter = null;
            $patient = $model->patient_id;
            $supplier = null;
            $items = $model->items;
        } elseif ($model instanceof TransferInvoice) {
            $type = 'transfer';
            $fromCenter = $model->from_center_id;
            $toCenter = $model->to_center_id;
            $patient = null;
            $supplier = null;
            $items = $model->items;
        } else {
            return;
        }

        // 1) create the movement header
        $movement = StockMovement::create([
            'type' => $type,
            'actor_type' => Auth::check() ? Auth::user()::class : null,
            'actor_id' => Auth::id(),
            'subject_type' => get_class($model),
            'subject_id' => $model->id,
            'from_center_id' => $fromCenter,
            'to_center_id' => $toCenter,
            'patient_id' => $patient,
            'supplier_id' => $supplier,
        ]);

        // 2) log each line‐item
        foreach ($items as $item) {
            $movement->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
            ]);
        }
    }

}
