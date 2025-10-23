<?php

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

        $data = [
            'patient' => $patient,
            'prescription' => $prescription,
        ];
        $pdf = Pdf::loadView('components.patient-info', $data)->setPaper('a4');

        return $pdf->stream();
        return view('components.patient-info', $data);
    })->name('patient-info');
});
