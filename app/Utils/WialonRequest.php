<?php

namespace App\Utils\Wialon;

use App\Services\LogService;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

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
            $token = "";

            $client = new Client(["verify" => false]);
            $response = $client->request('GET', 'https://hst-api.wialon.com/wialon/ajax.html?svc=token/login&sid=',
            [
                'form_params' =>
                [
                    'params' =>  '{"token":"'.$token.'","operateAs":"","appName":"","checkService":""}',
                    'sid'   =>  ''
                ]
            ]);
            $sessionWialon = json_decode($response->getBody()->getContents());

            return ["_ID" => $sessionWialon->eid, "_UID" => $sessionWialon->user->id];
        }
        catch (Exception $ex)
        {
            throw new Exception($ex->getMessage());
        }
    }
}
