<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Ficha del Vehículo | Aparcadero Municipal</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            border: 1px solid #ccc;
            padding: 5px;
        }

        .logo {
            text-align: center;
        }

        .logo>img {
            width: 138.875px;
            height: auto;
        }

        .titulo {
            font-weight: bold;
            text-align: center;
        }

        .seccion {
            margin-bottom: 10px;
        }

        .firmas {
            text-align: center;
        }
    </style>
</head>

<body>
    <table style="width: 100%;">
        <tr>
            <td colspan="1" class="logo">
                <img src="{{ public_path('assets/logos/logo3.png') }}" alt="Logo de la empresa">
            </td>
            <td colspan="3" class="titulo">
                <h1>Ficha de Ingreso y Salida del Vehículo</h1>
            </td>
        </tr>
        <tr>
            <td colspan="4"><strong>Fecha emisión:</strong> {{ date('d-m-Y H:i:s') }} </td>
        </tr>
    </table>
    <hr>
    <h2>Información de Ingreso</h2>
    <table class="seccion">
        <tr>
            <td><strong>Fecha:</strong></td>
            <td>{{ $registro->entrada->format('d-m-Y') }}</td>
            <td><strong>Hora:</strong></td>
            <td>{{ $registro->entrada->format('H:i:s') }}</td>
        </tr>
        <tr>
            <td><strong>Registrado por:</strong></td>
            <td colspan="3">{{ $registro->user_entrada->nombre }} {{ $registro->user_entrada->apellido_paterno }}
                {{ $registro->user_entrada->apellido_materno }}</td>
        </tr>
    </table>

    <h2>Información de Salida</h2>
    <table class="seccion">
        <tr>
            <td><strong>Fecha:</strong></td>
            <td>{{ $registro->salida !== null ? $registro->salida->format('d-m-Y') : '-' }}</td>
            <td><strong>Hora:</strong></td>
            <td>{{ $registro->salida !== null ? $registro->salida->format('H:i:s') : '-' }}</td>
        </tr>
        <tr>
            <td><strong>Registrado por:</strong></td>
            <td colspan="3">{{ $registro->user_salida !== null ? $registro->user_salida->nombre : '-' }}
                {{ $registro->user_salida !== null ? $registro->user_salida->apellido_paterno : '' }}
                {{ $registro->user_salida !== null ? $registro->user_salida->apellido_materno : '' }}</td>
        </tr>
    </table>
    <section>
        @if ($registro->vehiculo)
            <hr>
            <h2>Información del Vehículo</h2>
            <table class="seccion">
                <tr>
                    <td><strong>Número de Placa:</strong></td>
                    <td>{{ $registro->vehiculo->patente }}</td>
                    <td><strong>Tipo:</strong></td>
                    <td colspan="3">{{ $registro->vehiculo->tipo }}</td>
                </tr>
                <tr>
                    <td><strong>Marca:</strong></td>
                    <td>{{ $registro->vehiculo->marca }}</td>
                    <td><strong>Modelo:</strong></td>
                    <td>{{ $registro->vehiculo->modelo }}</td>
                    <td><strong>Color:</strong></td>
                    <td>{{ $registro->vehiculo->color }}</td>
                </tr>
            </table>
        @endif
    </section>
    <section>
        @if ($registro->vehiculo->observaciones->isNotEmpty())
            <hr>
            <h2>Observaciones</h2>
            <table class="seccion">
                <tr>
                    <td colspan="4">
                        <ol>
                            @foreach ($registro->vehiculo->observaciones as $observacion)
                                @if ($observacion->estado === 1)
                                    <li>
                                        Descripción : {{ $observacion->descripcion }} <br>
                                        Fecha : {{ $observacion->fecha_registro->format('d-m-Y H:i:s') }} <br>
                                    </li> <br>
                                @endif
                            @endforeach
                        </ol>
                    </td>
                </tr>
            </table>
        @endif
    </section>
</body>

</html>
