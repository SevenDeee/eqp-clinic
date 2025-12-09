<?php

use App\Models\Inventory;
use App\Models\InventoryOrder;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\json;

Route::get('/testing', function () {
    return view('components.prescription');
});

Route::middleware('auth')->group(function () {

    Route::get('/patient-info/{patient}/print/{prescription}', function ($patient, $prescription) {

        $patient = App\Models\Patient::findOrFail($patient);
        $prescription = App\Models\Prescription::findOrFail($prescription);

        $specs['frame_type'] = Inventory::where('id', InventoryOrder::where('order_id', Order::where('patient_id', $patient->id)->value('id'))->value('inventory_id'))->value('name');
        $specs['color'] = Inventory::where('id', InventoryOrder::where('order_id', Order::where('patient_id', $patient->id)->value('id'))->skip(1)->value('inventory_id'))->value('name');
        $specs['lens'] = Inventory::where('id', InventoryOrder::where('order_id', Order::where('patient_id', $patient->id)->value('id'))->skip(2)->value('inventory_id'))->value('name');

        $bill['amount'] = Order::where('patient_id', $patient->id)->value('amount');
        $bill['deposit'] = Order::where('patient_id', $patient->id)->value('deposit');
        $bill['balance'] = Order::where('patient_id', $patient->id)->value('balance');

        $data = [
            'patient' => $patient,
            'specs' => $specs,
            'bill' => $bill,
            'prescription' => $prescription,
        ];
        $pdf = Pdf::loadView('components.patient-info', $data)->setPaper('a4');

        return $pdf->stream();
        // return view('components.patient-info', $data);
    })->name('patient-info');
});
