<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Usuario;

class JwtAuth {

    public $key;

    public function __construct() {
        $this->key = 'C9fBxl1EWtYTL1/M8jfstw==';//cambiar clave por una mas segura
    }

    public function signup($email, $password, $getToken = null) {
        //Buscar si existe el usuario
        $user = Usuario::where([
            'correo' => $email,
            'clave' => $password
        ])->first();
        $signup = false;
        if (is_object($user)) {
            $signup = true;
        }
        //Generar el Token
        if ($signup) {
            $token = array(
                'sub' => $user->id,
                'correo' => $user->correo,
                'rol' => $user->rol,
                'nombre' =>$user->nombre.' '.$user->apellido,
                'documento' => $user->tipo_documento.' '.$user->numero_documento,
                'iat' => time(),
                'exp' => time() + 3000
            );

            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decode = JWT::decode($jwt, $this->key, ['HS256']);
            if (is_null($getToken)) {
                $data = $jwt;
            } else {
                $data = $decode;
            }

        } else {
            $data = array(
                'status' => 'error',
                'message' => 'el usuario o la clave ingresada no coinciden con los datos almacenados'
            );
        }
        //Devolver Token
        return $data;
    }

    public function checkToken($jwt, $getIdentity = false) {
        $auth = false;
        try {
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }

        if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        } else {
            $auth = false;
        }

        if ($getIdentity) {
            return $decoded;
        }
        return $auth;
    }
}
