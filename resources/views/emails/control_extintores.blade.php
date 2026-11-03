@extends('emails.layout')

@section('subject', 'Reporte de Control de Extintores')
@section('title', 'Reporte de Control de Extintores')

@section('content')
    <p style="font-size: 16px; color: #333;">
        Adjunto encontrará el archivo con el control de extintores correspondiente a:
    </p>

    <ul style="font-size: 16px; color: #333; padding-left: 20px;">
        <li><strong>Cliente:</strong> {{ $nombreCliente ?? '' }}</li>
        <li><strong>Localidad:</strong> {{ $nombreLocalidad ?? '' }}</li>
        <li><strong>Fecha:</strong> {{ $fechaRevision ?? '' }}</li>
    </ul>

    <p style="margin-top: 30px; font-size: 16px;">
        Atentamente,<br>
        <strong>Sepro</strong>
    </p>
@endsection

@section('firma')
    <div style="padding: 5px 5px; display: flex; align-items: left; justify-content: space-between;">
        <img src="https://seprolaff.com/img/nexo-firma.png" alt="Firma Sepro" style="height: 120px;">
    </div>
@endsection