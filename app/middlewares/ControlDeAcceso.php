<?php

use Firebase\JWT\JWT;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ControlDeAcceso
{
	public static function VerificarPermisoSocio($request, $handler){
		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);
		$response = new Response();
		
		$data = AutentificadorJWT::ObtenerData($token);
		if (!empty($token) && $data->rol = 'socio') {
			$response = $handler->handle($request);
		}else{
			$response->getBody()->write(json_encode( array('mensaje' => 'Acceso denegado')));
		}
		return $response
			->withHeader('Content-Type', 'application/json');
	}
}