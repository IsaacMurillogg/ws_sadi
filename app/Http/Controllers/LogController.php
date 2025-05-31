<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{

    public static $URL_LOG = 'https://log.wsmprastreo.com.mx/api/';
    //public static $URL_LOG = 'http://127.0.0.1:8000/api/';

    public function addLog($idM, $movimiento, $tabla, $estatus = 'Info')
    {
        $ban = true;
        $ldate = date('Y-m-d H:i:s');
        try {
            $email = 'cronjob@host.com';
            $name = 'hosting';
            if (Auth::check()) {
                $email = Auth::user()->email;
                $name = Auth::user()->name;
            }
            $response = Http::accept('application/json')->post('https://log.wsmprastreo.com.mx/api/agregar', [
                'sistema' => 'MASA-CONCRETOS',
                'usuario' => $email,
                'nombre' => $name,
                'id_movimiento' => $idM,
                'movimiento' => $movimiento,
                'tabla' => $tabla,
                'fecha' => $ldate,
                'estatus' => $estatus
            ]);
            if ($response->successful()) {
                $ban = true;
            } else {
                $ban = false;
            }
        } catch (\Throwable $th) {
            $ban = false;
        }

        if ($ban) {
            Log::info('Movimiento exitoso: ' . $idM . ' ' . $movimiento . '/ Usuario: ' . $email . ' ' . $name . '/ Tabla: ' . $tabla);
        } else {
            Log::error('Movimiento sin exito: ' . $idM . ' ' . $movimiento . '/ Usuario: ' . $email . ' ' . $name . '/ Tabla: ' . $tabla);
        }
    }

    public static function updateLog()
    {
        try {

            // Crear un cliente Guzzle
            $client = new Client();

            $unidades = [];
            foreach (Unit::all() as $unit) {
                $unit->placa == null ? $unit->placa = 'N/A' : $unit->placa;
                array_push($unidades, [
                    "hora_reporte" => $unit->lastMessage ?? 'N/A',
                    "id_satech" => $unit->id_wialon ?? 'N/A',
                    "placa" => $unit->placa ,
                    "unidad" => $unit->name ?? 'U/D'
                ]);
            }


            $data = [
                "id" => "2kLTxnYpSQYRcP2Hzwtb",
                "datos" => [
                    "nombre" => "Masa Concretos",
                    "horario_actualizacion" => date('Y-m-d H:i:s'),
                    "password" => "*************",
                    "url" => "-",
                    "usuario_satech" => "*************",
                    "unidades" => $unidades
                ]
            ];

            $response = $client->request('POST', LogController::$URL_LOG.'v1/registro/log', [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ],
                'json' => $data
            ]);

            // Obtener el cuerpo de la respuesta
            $response->getBody();
            Log::error("Registro exitoso en Log");
        } catch (Exception $e) {
            Log::error("update error: " . $e->getMessage());
        }
    }
}
