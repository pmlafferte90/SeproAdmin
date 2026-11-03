<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('subject', 'Correo Sepro')</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f8f9fa;">

    {{-- Header --}}
    <div style="background-color: #19346A; color: #ffffff; padding: 20px 30px; display: flex; align-items: center; justify-content: space-between;">
        <img src="https://seprolaff.com/img/logo-sepro-blanco.png" alt="Logo Sepro" style="height: 60px;">
        <h2 style="margin: 0; font-size: 20px; text-align: right; flex: 1;">@yield('title', 'Notificación')</h2>
    </div>

    {{-- Contenido dinámico --}}
    <div style="padding: 30px; background-color: #ffffff;">
        @yield('content')
        @yield('firma')
    </div>

    {{-- Footer --}}
    <div style="background-color: #e9ecef; padding: 20px; text-align: center; font-size: 12px; color: #555;">
        Este correo electrónico es confidencial y también puede ser privilegiado y/o propiedad de Sepro.
    </div>

</body>
</html>
