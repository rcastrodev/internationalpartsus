<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Email desde internationalparts.us</title>
</head>
<body>
    <p>Este correo ha sido enviado desde el formulario de contacto en internationalparts.us</p>
    <ul>
        <li>Nombre: {{ $data['name'] }}</li>
        <li>Email: {{ $data['email'] }}</li>
        <li>Mensaje: {{ $data['message'] }}</li>
    </ul>
</body>
</html>