<?php

namespace App\Utils\Wialon;

use App\Services\LogService;
use Exception;
use GuzzleHttp\Client;

class WialonRequest
{
    /**
     * Authenticate and obtain session information from the Wialon API.
     *
     * This function sends a request to the Wialon API to obtain a session token
     * using a predefined authentication token. It returns the session ID and user ID.
     *
     * @return array An array containing "_ID" (session ID) and "_UID" (user ID).
     * @throws Exception If there is an error during the API request or authentication.
     */
    public static function login() : array | Exception
    {
        try
        {
            // $token = LogService::getToken();

            // if (empty($token))
            // {
            //     LogService::renewToken();
            //     return self::login();
            // }

            $token = "---AGREGAR TOKEN PARA ACTIVAR DE NUEVO---";

            $client = new Client();
            $response = $client->request('GET', 'https://hst-api.wialon.com/wialon/ajax.html?svc=token/login&sid=',
            [
                'form_params' =>
                [
                    'params' =>  '{"token":"'.$token.'","operateAs":"","appName":"","checkService":""}',
                    'sid'   =>  ''
                ]
            ]);

            $sessionWialon = json_decode($response->getBody()->getContents());

            // if(isset($sessionWialon->error))
            // {
            //     if (strcmp(strval($sessionWialon->reason), "INVALID_AUTH_TOKEN") === 0)
            //     {
            //         LogService::renewToken();
            //         return self::login();
            //     }
            // }

            return ["_ID" => $sessionWialon->eid, "_UID" => $sessionWialon->user->id];
        }
        catch (Exception $ex)
        {
            throw new Exception($ex->getMessage());
        }
    }
}
