<?php

class Logger
{
    public static function VerificarCredenciales($request, $handler)
    {
        $method = $request->getMethod();

        if($method == 'GET'){
            $response = $handler->handle($request);
            $response->getBody()->write('El método de la solicitud es: ' . $method);
        }
        else if($method == 'POST'){
            $response = $handler->handle($request);
            $response->getBody()->write('El método de la solicitud es: ' . $method);
            $body = $request->getParsedBody();
            $nombre = $body['nombre'];
            $perfil = $body['perfil'];
            
            if($perfil == 'administrador'){
                $response->getBody()->write('Bienvenido ' . $nombre);
            } else {
                $response->getBody()->write('Usuario no autorizado');
            }
            
        }
        return $response;
    }
}