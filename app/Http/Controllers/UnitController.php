<?php

namespace App\Http\Controllers;

use App\Models\unidad_historial;
use App\Models\Unit;
use App\Services\LogService;
use App\Utils\Wialon\WialonRequest;
use Carbon\Carbon;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Object_;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

class UnitController extends Controller
{
    public function error()
    {
        return response()->json(['Error' => 'No se encontro la dirección'], Response::HTTP_NOT_FOUND);
    }

    public function name(Request $request)
    {
        try {
            $unidad = Unit::where('name', $request->name)->first();
            if (!$unidad) {
                $unidad = [
                    "name" => "La unidad no se encuentra registrada",
                    "placa" => "NR",
                    "latitud" => "0",
                    "longitud" => "0",
                    "velocidad" => "0",
                    "last_message" => "0000-00-00 00:00:00"
                ];
            }

            return response()->json($unidad, Response::HTTP_OK);
        } catch (Exception $ex) {
            Log::error($ex->getTraceAsString());
            LogService::sendToLog($ex->getMessage());
            return response()->json(['error' => 'Hubo un error inesperado, vuelva a intentarlo'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public static function indexApiWialon()
    {
        try {
            set_time_limit(1200000);

            DB::beginTransaction();

            $unidades = Unit::all();
            $unid = new stdClass;
            $dbUnidades = [];

            foreach ($unidades as $u) {
                $unid->name = $u->name;
                $unid->placa = $u->placa;
                $unid->latitud = $u->latitud;
                $unid->longitud = $u->longitud;
                $unid->lastMessage = $u->lastMessage;

                array_push($dbUnidades, $u);
            }

            DB::commit();

            return response()->json($dbUnidades);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex->getTraceAsString());
            LogService::sendToLog($ex->getMessage());
            return response()->json(['error' => 'Hubo un error inesperado, vuelva a intentarlo'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function truncar($numero, $digitos)
    {
        $cadena = strval($numero);

        $val = explode('.', $cadena);

        if (sizeof($val) > 1)
            $num = $val[0] . '.' . substr($val[1], 0, $digitos);
        else
            $num = $cadena;

        return $num;
    }

    public static function guardarUnidades()
    {
        $crtU = new UnitController();

        try {
            set_time_limit(1200000);

            $client = new Client();

            $_ID = WialonRequest::login();

            if(isset($_ID["_ID"])){
                $_ID = $_ID["_ID"];
            } else {
                $_ID = '';
            }

            $responseSens = $client->request('GET', 'https://hst-api.wialon.com/wialon/ajax.html?svc=core/update_data_flags&sid=' . $_ID, [
                'form_params' => [
                    'params' =>  '{"spec":[{"type":"type","data":"avl_unit","flags":8393865,"mode":0}]}',
                    'sid'   =>  $_ID
                ]
            ]);

            DB::beginTransaction();

            foreach (json_decode($responseSens->getBody()->getContents()) as $unidad) {
                if ($dbUnid = Unit::where('id_wialon', '=', json_encode($unidad->i))->first()) {
                    $dbUnid->name = $unidad->d->nm;
                    $dbUnid->lastMessage = date('Y-m-d H:i:s', $unidad->d->lmsg->t);
                    $dbUnid->updated_at = Carbon::now('GMT-06:00');

                    if(isset($unidad->d->pos->y)){
                        $dbUnid->latitud = $crtU->truncar($unidad->d->pos->y, 6);
                    } else {
                        $dbUnid->latitud = 0;
                    }

                    if(isset($unidad->d->pos->x)){
                        $dbUnid->longitud = $crtU->truncar($unidad->d->pos->x, 6);
                    } else {
                        $dbUnid->longitud = 0;
                    }

                    if (isset($unidad->d->flds)) {
                        foreach ($unidad->d->flds  as $profile) {
                            if ($profile->n == "Placa") {
                                $dbUnid->placa = $profile->v;
                            } else {
                                $dbUnid->placa = 'NR';
                            }
                        }
                    } else {
                        $dbUnid->placa = 'NR';
                    }

                    Log::info('Unidad actualizada: ' . json_encode($dbUnid));
                    $dbUnid->save();
                } else {
                    $nuevaUnidad = new Unit();
                    $nuevaUnidad->id_wialon = $unidad->i;
                    $nuevaUnidad->name = $unidad->d->nm;
                    $nuevaUnidad->latitud = $crtU->truncar($unidad->d->pos->y, 6);
                    $nuevaUnidad->longitud = $crtU->truncar($unidad->d->lmsg->pos->x, 6);
                    $nuevaUnidad->lastMessage = date('Y-m-d H:i:s', $unidad->d->lmsg->t);

                    if (isset($unidad->d->flds)) {
                        foreach ($unidad->d->flds  as $profile) {
                            if ($profile->n == "Placa") {
                                $nuevaUnidad->placa = $profile->v;
                            } else {
                                $nuevaUnidad->placa = 'NR';
                            }
                        }
                    } else {
                        $nuevaUnidad->placa = 'NR';
                    }

                    Log::info('Nueva unidad: ' . json_encode($nuevaUnidad));
                    $nuevaUnidad->save();
                }
            }

            UnitController::eliminarRepetidas();

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            Log::info($ex->getTraceAsString());
            LogService::sendToLog($ex->getMessage());
            return response()->json(['error' => 'Hubo un error inesperado, vuelva a intentarlo'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public static function eliminarRepetidas()
    {
        try {

            $unidades = Unit::all();
            $sum = 0;

            foreach ($unidades as $u) {
                if (UnitController::calcularMinutos($u->updated_at) >= 5) {
                    $u->delete();
                    Log::info('Unidad eliminada por repeticion: ' . $u->name);
                    $sum += 1;
                }
            }

            if ($sum == 0)
                Log::info('No se borro ninguna unidad');
        } catch (Exception $ex) {
            Log::error($ex->getTraceAsString());
            LogService::sendToLog($ex->getMessage());
        }
    }

    public static function calcularMinutos($fechaDB)
    {
        $fechaActual = new DateTime();
        $fechaEspecifica = new DateTime($fechaDB);

        $diferencia = $fechaActual->diff($fechaEspecifica);

        $minutos = ($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i;
        return $minutos;
    }
}
