@extends('emails.layout')

@section('subject', 'Acceso a Sepro')
@section('title', 'Acceso a Sepro')

@section('content')
    <p style="font-size: 16px; color: #333;">
    Saludos {{ $nombre ?? 'usuario' }},
    </p>

    <p style="font-size: 14px; color: #333;">
    Estas son tus credenciales de acceso:
    </p>

    <ul style="font-size: 14px; color: #333; padding-left: 20px;">
        <li><strong>Usuario:</strong> {{ $usuario }}</li>
        <li><strong>Contraseña:</strong> {{ $password }}</li>
    </ul>

    <p style="margin-top: 30px; font-size: 16px;">
        Atentamente,<br>
        <strong>Sepro</strong>
    </p>
@endsection
