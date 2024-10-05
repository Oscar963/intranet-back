<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
    <style>
        body {
            font-family: "Cereal", "Helvetica", Helvetica, Arial, sans-serif;
            background-color: #f6f6f6;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 95%;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            background-color: rgb(0, 70, 147);
            width: 100%;
        }

        .header img {
            width: 100px;
        }

        .content {
            text-align: left;
            max-width: 600px;
            margin: auto;
            padding: 20px 0;
        }

        .content h1 {
            font-size: 24px;
            margin: 0;
            color: #333333;
        }

        .content p {
            font-size: 16px;
            color: #484848;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 16px 0;
            font-size: 16px;
            color: #ffffff !important;
            background-color: #FF8800;
            text-decoration: none;
            border-radius: 5px;
        }

        .divider {
            border: 1px solid #cacaca;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #fff;
            background-color: rgb(0, 70, 147);
            width: 100%;
            line-height: 1.5em;
            padding: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ $logoUrl }}" alt="Logo">
        </div>
        <div class="content">
            <h2>Hola,</h2>
            <p>Hemos recibido una solicitud para restablecer la contraseña.</p>
            <p>Si no fuiste tú quien envió la solicitud, ignora este mensaje. En caso contrario, puedes restablecer tu
                contraseña.</p>
            <a href="{{ $resetUrl }}" class="button">Restablecer contraseña</a>
            <div class="divider"></div>

            <p>Gracias. <br> <small>Dirección de Innovación y Desarrollo Institucional - Oficina de
                    ingeniería y redes</small></p>
        </div>
        <div class="footer">
            <p>©{{ now()->year }} {{ config('app.name') }}. Todos los derechos reservados.</p>
        </div>
    </div>
</body>

</html>
