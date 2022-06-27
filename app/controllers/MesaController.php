<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
	public function cargarUno($request, $response, $args)
	{
		$mensaje = 'Ha habido un error';
		$parametros = $request->getParsedBody();
		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);

		$usuario = AutentificadorJWT::obtenerData($token);

		$codigo = $parametros['codigo'];
		$capacidad = $parametros['capacidad'];

		if (Mesa::obtenerMesa($codigo)) {
			$mensaje = 'Ya existe una mesa con ese codigo';
		} else {
			// Creamos la mesa
			$mesa = new Mesa();
			$mesa->codigo = $codigo;
			$mesa->capacidad = $capacidad;
			$idMesa = $mesa->crearMesa();

			$estadoMesa = new Estado();
			$estadoMesa->idEntidad = $idMesa;
			$estadoMesa->entidad = 'Mesa';
			$estadoMesa->descripcion = Estado::getEstadoDefaultMesa();
			$estadoMesa->usuarioCreador = $usuario->nombre;
			$estadoMesa->guardarEstado();

			$mensaje = 'Mesa creada con éxito';
		}

		$payload = json_encode(array("mensaje" => $mensaje));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}


	public function traerUno($request, $response, $args)
	{
		// Buscamos mesa por código
		$mesa = $args['codigo'];
		$mesa = Mesa::obtenerMesa($mesa);
		$payload = json_encode($mesa);

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function traerTodos($request, $response, $args)
	{
		$lista = Mesa::obtenerTodos();
		$payload = json_encode(array("listaMesa" => $lista));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}
	
	public function modificarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();

		$id = $parametros['id'];
		Mesa::modificarMesa($id);

		$payload = json_encode(array("mensaje" => "Mesa modificado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function borrarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();

		$mesaId = $parametros['id'];
		Mesa::borrarMesa($id);

		$payload = json_encode(array("mensaje" => "Mesa borrado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	
	public static function cerrarMesa($request, $response, $args){
		$mensaje = 'Ha habido un error';
		$parametros = $request->getParsedBody();

		$codigoMesa = $parametros['codigoMesa'];
		
		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);
		$usuario = AutentificadorJWT::obtenerData($token);

		$mesa = Mesa::obtenerMesa($codigoMesa);
		if ($usuario->rol == 'socio' || $usuario->rol == 'mozo') {
			if ($mesa->estado == STATUS_MESA_PAGANDO) {


				$estado_mesa = new Estado();
				$estado_mesa->idEntidad = $mesa->id;
				$estado_mesa->descripcion = STATUS_MESA_CERRADA;
				$estado_mesa->entidad = 'Mesa';
				$estado_mesa->usuarioCreador = $usuario->nombre;
				$estado_mesa->guardarEstado();

				$mensaje = 'Se cerró la mesa ' . $mesa->codigo;
			} else {
				$mensaje = 'El cliente aún no pagó el pedido';
			}
		} else {
			$mensaje = 'Usuario no autorizado';
		}

		$payload = json_encode(array("mensaje" => $mensaje));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}
}
