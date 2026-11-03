<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ControlExtintorExport implements FromView, WithDrawings, WithDefaultStyles, WithEvents
{
    protected $controlExtintores, $empresa, $selected;

    // Constructor para recibir los datos de extintores
    public function __construct($dataControlExtintores, $empresa, array $selected)
    {
        $controlExtintores = [];

        foreach ($dataControlExtintores as $item) {
            $area = $item['area_nombre'];
            if (!isset($controlExtintores[$area])) {
                $controlExtintores[$area] = [];
            }
            $controlExtintores[$area][] = $item;
        }

        $this->controlExtintores = $controlExtintores;
        $this->empresa = $empresa;
        $this->selected = $selected;
    }

    public function view(): View
    {
        // Pasar los datos de extintores directamente a la vista de exportación
        $headersMap = [
            'fecha_revision' => [
                'label' => 'FECHA DE REVISIÓN',
                'style' => 'width: 100px; text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; word-wrap: break-word;'
            ],
            'codigo' => [
                'label' => 'COD',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 75px;'
            ],
            'area_nombre' => [
                'label' => 'ÁREA',
                'style' => 'width: 150px; text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2;'
            ],
            'ubicacion' => [
                'label' => 'UBICACIÓN',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 270px;'
            ],
            'clase' => [
                'label' => 'CLASE',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 80px;'
            ],
            'marca' => [
                'label' => 'MARCA',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 80px;'
            ],
            'peso' => [
                'label' => 'PESO',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 80px;'
            ],
            'manguera' => [
                'label' => 'MANGUERA',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 65px; word-wrap: break-word;'
            ],
            'manometro' => [
                'label' => 'MANÓMETRO',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 65px; word-wrap: break-word;'
            ],
            'seguro' => [
                'label' => 'SEGURO',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 65px; word-wrap: break-word;'
            ],
            'cilindro' => [
                'label' => 'CILINDRO',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 65px; word-wrap: break-word;'
            ],
            'fecha_recarga' => [
                'label' => 'FECHA RECARGA',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 120px; word-wrap: break-word;'
            ],
            'fecha_prox_recarga' => [
                'label' => 'FECHA PRÓXIMA RECARGA',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 120px; word-wrap: break-word;'
            ],
            'area_seguridad' => [
                'label' => 'ÁREA DE SEGURIDAD',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; word-wrap: break-word; width: 85px;'
            ],
            'instructivo_uso' => [
                'label' => 'INSTRUCTIVO DE USO',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 85px;  word-wrap: break-word;'
            ],
            'instructivo_clasif' => [
                'label' => 'INSTRUCTIVO DE CLASIFICACIÓN',
                'style' => 'font-size: 7px; text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 92px; word-wrap: break-word;'
            ],
            'flecha_ubicacion' => [
                'label' => 'FLECHA DE UBICACIÓN',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 85px; word-wrap: break-word;'
            ],
            'silueta' => [
                'label' => 'SILUETA DE EXTINTOR',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 85px;  word-wrap: break-word;'
            ],
            'rect_instalacion' => [
                'label' => 'RECTANGULO DE INSTALACIÓN',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 95px; word-wrap: break-word;'
            ],
            'hidrante' => [
                'label' => 'EXTINTOR DENTRO DE HIDRANTE',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 85px; word-wrap: break-word;'
            ],
            'dimension_vidrio' => [
                'label' => 'DIMENSIÓN VIDRIO/ACRÍLICO',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 85px; word-wrap: break-word;'
            ],
            'fecha_fabricacion' => [
                'label' => 'FECHA FABRICACIÓN',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 85px; word-wrap: break-word;'
            ],
            'fecha_prueba_hidros' => [
                'label' => 'FECHA PRUEBA HIDROSTÁTICA',
                'style' => 'font-size: 7px; text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 95px; word-wrap: break-word;'
            ],
            'novedad_marbete' => [
                'label' => 'MARBETE',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 85px; word-wrap: break-word;'
            ],
            'observaciones_nomen' => [
                'label' => 'OBSERVACIONES',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 170px;  word-wrap: break-word;'
            ],
            'observaciones' => [
                'label' => 'OBSERVACIONES INTERNAS',
                'style' => 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 150px;'
            ],
        ];


        return view('exports.control_extintor_export', [
            'controlExtintores' => $this->controlExtintores,
            'empresa' => $this->empresa,
            'selected'      => $this->selected,    // las keys a mostrar
            'headersMap'    => $headersMap,
        ]);
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
        $nomenclatura->setCoordinates('D2'); // puede ser otra columna si deseas separarla

        return [$logo, $nomenclatura];
    }

    public function defaultStyles(Style $defaultStyle)
    {
        // Configure the default styles
         $defaultStyle->getFont()->setName('Arial');
         return $defaultStyle->getFont()->setSize(8);
    }

    public function styles(Worksheet $sheet)
    {
        // Calcula hasta qué fila llega la tabla
        $startRow = 7;
        $lastRow = $startRow;
        foreach ($this->controlExtintores as $area => $items) {
            $lastRow += 1; // fila título de área
            $lastRow += count($items); // filas de datos
        }

        // Define la última columna según tu export real (ajusta si tienes más)
        $lastCol = 'AD';

        // Bordes gruesos y negros en toda la tabla
        $sheet->getStyle("A{$startRow}:{$lastCol}{$lastRow}")
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => ['rgb' => '000000'], // negro sólido
                    ],
                ],
            ]);

        // Remarcar aún más el encabezado (fondo gris y negrita)
        $sheet->getStyle("A{$startRow}:{$lastCol}{$startRow}")
            ->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD9D9D9'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Detecta el rango usado (desde A1 hasta la última columna y fila)
                $highestColumn = $sheet->getHighestColumn();
                $highestRow    = $sheet->getHighestRow();
                $cellRange     = 'A5:' . $highestColumn . $highestRow;

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
