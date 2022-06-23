<?php
require_once './models/Encuesta.php';
require_once './interfaces/IApiUsable.php';
require_once './middlewares/AutentificadorJWT.php';

class EncuestaController extends Encuesta implements IApiUsable
{
	public function CargarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();

		$nombre = $parametros['nombre'];
		$clave = $parametros['clave'];
		$rol = $parametros['rol'];

		// Creamos el encuesta
		$usr = new Encuesta();
		$usr->nombre = $nombre;
		$usr->clave = $clave;
		$usr->rol = $rol;
		$idEncuesta = $usr->crearEncuesta();

		$payload = json_encode(array("mensaje" => "Encuesta creada con exito"));

		$response->getBody()->write($payload);
		return $response
		->withHeader('Content-Type', 'application/json');
	}


	public function TraerUno($request, $response, $args)
	{
		// Buscamos encuesta por nombre
		$usr = $args['nombre'];
		$encuesta = Encuesta::obtenerEncuesta($usr);
		$payload = json_encode($encuesta);

		$response->getBody()->write($payload);
		return $response
		->withHeader('Content-Type', 'application/json');
	}

	public function TraerTodos($request, $response, $args)
	{
		$lista = Encuesta::ObtenerTodos();
		$payload = json_encode(array("listaEncuesta" => $lista));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}
	
	public function ModificarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();

		$nombre = $parametros['nombre'];
		$clave = $parametros['clave'];
		$rol = $parametros['rol'];
		$id = $parametros['id'];

		// Creamos el encuesta
		$usr = new Encuesta();
		$usr->nombre = $nombre;
		$usr->clave = $clave;
		$usr->rol = $rol;
		$usr->id = $id;

		$usr->modificarEncuesta();


		$payload = json_encode(array("mensaje" => "Encuesta modificado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function BorrarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();

		$encuestaId = $parametros['id'];
		Encuesta::borrarEncuesta($id);

		$payload = json_encode(array("mensaje" => "Encuesta borrado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function VerificarCredenciales($request, $response, $args){
		$parametros = $request->getParsedBody();
		$nombreEncuesta = $parametros['nombre'];
		$clave = $parametros['clave'];
		$output = 'Credenciales incorrectas.';

		$encuesta = Encuesta::obtenerEncuesta($nombreEncuesta, $clave);
		if (password_verify($clave, $encuesta->clave)) {
			$encuesta->clave = null;
			$output = AutentificadorJWT::crearToken($encuesta);
			
			$log = new Log();
			$log->idEncuestaCreador = $encuesta->id;
			$log->descripcion = Log::obtenerDescripcionLogLogin();
			$log->guardarLog();
		}

		$payload = json_encode(array("respuesta" => $output));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}
}
