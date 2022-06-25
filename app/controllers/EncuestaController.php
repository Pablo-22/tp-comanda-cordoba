<?php
require_once './models/Encuesta.php';
require_once './interfaces/IApiUsable.php';
require_once './middlewares/AutentificadorJWT.php';

class EncuestaController extends Encuesta implements IApiUsable
{
	public function CargarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();

		$codigoMesa = $parametros['codigoMesa'];
		$codigoPedido = $parametros['codigoPedido'];
		$puntuacionMesa = $parametros['puntuacionMesa'];
		$puntuacionMozo = $parametros['puntuacionMozo'];
		$puntuacionCocinero = $parametros['puntuacionCocinero'];
		$puntuacionRestaurante = $parametros['puntuacionRestaurante'];
		$descripcion = $parametros['descripcion'];

		// Creamos el encuesta
		$encuesta = new Encuesta();
		$encuesta->codigoMesa = $codigoMesa;
		$encuesta->codigoPedido = $codigoPedido;
		$encuesta->puntuacionCocinero = $puntuacionCocinero;
		$encuesta->puntuacionMozo = $puntuacionMozo;
		$encuesta->puntuacionMesa = $puntuacionMesa;
		$encuesta->puntuacionRestaurante = $puntuacionRestaurante;
		$encuesta->descripcion = $descripcion;
		$idEncuesta = $encuesta->crearEncuesta();

		$payload = json_encode(array("mensaje" => "Encuesta creada con exito"));

		$response->getBody()->write($payload);
		return $response
		->withHeader('Content-Type', 'application/json');
	}


	public function TraerUno($request, $response, $args)
	{
		$encuesta = $args['id'];
		$encuesta = Encuesta::obtenerEncuestaDB($encuesta);
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

	public function TraerMejoresComentarios($request, $response, $args)
	{
		$lista = Encuesta::ObtenerMejoresComentarios();

		$payload = json_encode(array("listaEncuesta" => $lista));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}
	
	public function ModificarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();

		$codigoMesa = $parametros['codigoMesa'];
		$codigoPedido = $parametros['codigoPedido'];
		$puntuacionMesa = $parametros['puntuacionMesa'];
		$puntuacionMozo = $parametros['puntuacionMozo'];
		$puntuacionCocinero = $parametros['puntuacionCocinero'];
		$puntuacionRestaurante = $parametros['puntuacionRestaurante'];
		$descripcion = $parametros['descripcion'];

		// Creamos el encuesta
		$encuesta = new Encuesta();
		$encuesta->codigoMesa = $codigoMesa;
		$encuesta->codigoPedido = $codigoPedido;
		$encuesta->puntuacionCocinero = $puntuacionCocinero;
		$encuesta->puntuacionMozo = $puntuacionMozo;
		$encuesta->puntuacionMesa = $puntuacionMesa;
		$encuesta->puntuacionRestaurante = $puntuacionRestaurante;
		$encuesta->descripcion = $descripcion;
		$idEncuesta = $encuesta->crearEncuesta();

		$encuesta->modificarEncuesta();


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
}
