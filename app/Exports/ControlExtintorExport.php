<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromView;

class ControlExtintorExport implements FromView
{
    protected $controlExtintores, $empresa;

    // Constructor para recibir los datos de extintores
    public function __construct($controlExtintores, $empresa)
    {
        $this->controlExtintores = $controlExtintores;
        $this->empresa = $empresa;
    }

    public function view(): View
    {
        // Pasar los datos de extintores directamente a la vista de exportación

        return view('exports.control_extintor_export', [
            'controlExtintores' => $this->controlExtintores,
            'empresa' => $this->empresa,
        ]);
    }
}
