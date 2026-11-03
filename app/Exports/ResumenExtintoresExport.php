<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ResumenExtintoresExport implements FromView, WithDrawings, WithDefaultStyles, WithEvents
{
    protected $data, $empresa, $localidad;

    public function __construct($data, $empresa, $localidad)
    {
        $this->data = $data;
        $this->empresa = $empresa;
        $this->localidad = $localidad;
    }

    public function view(): View
    {
        return view('exports.resumen_extintores', [
            'rows' => $this->data,
            'empresa' => $this->empresa,
            'localidad' => $this->localidad,
        ]);
    }


    public function defaultStyles(Style $defaultStyle)
    {
         $defaultStyle->getFont()->setName('Arial');
         return $defaultStyle->getFont()->setSize(8);
    }

    public function drawings()
    {
        $logo  = new Drawing();
        $logo ->setName('Logo');
        $logo ->setDescription('Company Logo');
        $logo ->setPath(public_path('img/logo-sepro.png'));
        $logo ->setHeight(150); // Adjust the height of the logo
        $logo ->setCoordinates('A2'); // Position the logo in the Excel sheet
        $logo->setOffsetX(20);
        $logo->setOffsetY(20);

        $nomenclatura = new Drawing();
        $nomenclatura->setName('Nomenclaturas');
        $nomenclatura->setDescription('Nomenclaturas');
        $nomenclatura->setPath(public_path('img/nomenclaturas.jpg')); // Imagen cargada
        $nomenclatura->setHeight(300); // ajusta si es necesario
        $nomenclatura->setCoordinates('C2'); // puede ser otra columna si deseas separarla

        return [$logo, $nomenclatura];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Detecta el rango usado (desde A1 hasta la última columna y fila)
                $highestColumn = $sheet->getHighestColumn();
                $highestRow    = $sheet->getHighestRow();
                $cellRange     = 'B6:' . $highestColumn . $highestRow;

                // Aplica un borde fino a todas las celdas
                $sheet->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
            },
        ];
    }
}