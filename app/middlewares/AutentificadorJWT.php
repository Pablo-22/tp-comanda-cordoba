<?php

use Firebase\JWT\JWT;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AutentificadorJWT
{
    private static $claveSecreta = 'T3sT$JWT';
    private static $tipoEncriptacion = ['HS256'];

    public static function crearToken($datos)
    {
        $ahora = time();
        $payload = array(
            'iat' => $ahora,
            'exp' => $ahora + (60000),
            'aud' => self::aud(),
            'data' => $datos,
            'app' => "TP1"
        );
        return JWT::encode($payload, self::$claveSecreta);
    }

    public static function verificarToken($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        try {
            $decodificado = JWT::decode(
                $token,
                self::$claveSecreta,
                self::$tipoEncriptacion
            );
        } catch (Exception $e) {
            throw $e;
        }
        if ($decodificado->aud !== self::aud()) {
            throw new Exception("No es el usuario valido");
        }
    }


    public static function obtenerPayLoad($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
    }

    public static function obtenerData($token)
    {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        )->data;
    }

    private static function aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }

	public static function verificarAcceso($request, $handler){
		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);
		$response = new Response();

		if (!empty($token)) {
			try {
				AutentificadorJWT::verificarToken($token);
			} catch (\Throwable $th) {
				$response->getBody()->write(json_encode( array('mensaje' => 'El token no es válido')));
				return $response
					->withHeader('Content-Type', 'application/json')
					->withStatus(401);
			}
			$response = $handler->handle($request);
		} else {
			$response->getBody()->write(json_encode( array('mensaje' => 'El token está vacío')));
		}
		return $response
			->withHeader('Content-Type', 'application/json');
	}
}