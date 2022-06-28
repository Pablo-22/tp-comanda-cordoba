<?php
require_once './models/Usuario.php';
require_once './models/Rol.php';
require_once './interfaces/IApiUsable.php';
require_once './middlewares/AutentificadorJWT.php';

class UsuarioController extends Usuario implements IApiUsable
{
	public function cargarUno($request, $response, $args)
	{
		$mensaje = 'Ha habido un error';
		$parametros = $request->getParsedBody();

		$nombre = isset($parametros['nombre']) ? $parametros['nombre'] : null;
		$clave = isset($parametros['clave']) ? $parametros['clave'] : null;
		$rol = isset($parametros['rol']) ? $parametros['rol'] :  null;
		$sector = $parametros['sector'] ?: '';

		if ($nombre && $clave && $rol) {
			if (Usuario::obtenerUsuario($nombre)) {
				$mensaje = 'Ya existe un usuario con ese nombre';
			} else {
				// Creamos el usuario
				$usuario = new Usuario();
				$usuario->nombre = $nombre;
				$usuario->clave = $clave;
				$usuario->rol = $rol;
				$usuario->sector = $sector;
				$idUsuario = $usuario->crearUsuario();
	
				$estadoUsuario = new Estado();
				$estadoUsuario->idEntidad = $idUsuario;
				$estadoUsuario->Descripcion = STATUS_USUARIO_DEFAULT;
				$estadoUsuario->usuarioCreador = $nombre;
				$estadoUsuario->guardarEstado();
	
				$mensaje = 'Usuario creado con éxito';
			}
		}else {
			$mensaje = 'Faltan parámetros';
			$payload = json_encode(array("mensaje" => $mensaje));

			$response->getBody()->write($payload);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus(400);
		}

		$payload = json_encode(array("mensaje" => $mensaje));

		$response->getBody()->write($payload);
		return $response
		->withHeader('Content-Type', 'application/json');
	}

	public function traerUno($request, $response, $args)
	{
		// Buscamos usuario por nombre
		$usr = isset($args['nombre']) ? $args['nombre'] : null;
		if ($usr) {
			$usuario = Usuario::obtenerUsuario($usr);
			$payload = json_encode($usuario);
		} else {
			$response->getBody()->write( json_encode(array("mensaje" => "Faltan parámetros")));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus(400);
		}

		$response->getBody()->write($payload);
		return $response
		->withHeader('Content-Type', 'application/json');
	}

	public function traerTodos($request, $response, $args)
	{
		$lista = Usuario::obtenerTodos();
		$payload = json_encode(array("listaUsuario" => $lista));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}
	
	public function modificarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();

		$nombre = isset($parametros['nombre']) ? $parametros['nombre'] : null;
		$clave = isset($parametros['clave']) ? $parametros['clave'] : null;
		$rol = isset($parametros['rol']) ? $parametros['rol'] :  null;
		$sector = $parametros['sector'] ?: '';
		$id = isset($parametros['id']) ? $parametros['id'] : null;

		if ($nombre && $clave && $rol && $id) {
			// Creamos el usuario
			$usr = new Usuario();
			$usr->nombre = $nombre;
			$usr->clave = $clave;
			$usr->rol = $rol;
			$usr->sector = $sector;
			$usr->id = $id;

			$usr->modificarUsuario();
		} else {
			$response->getBody()->write( json_encode(array("mensaje" => "Faltan parámetros")));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus(400);
		}

		$payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function borrarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();

		$usuarioId = $parametros['id'];
		Usuario::borrarUsuario($id);

		$payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function VerificarCredenciales($request, $response, $args){
		$parametros = $request->getParsedBody();
		$nombreUsuario = $parametros['nombre'];
		$clave = $parametros['clave'];
		$output = 'Credenciales incorrectas.';

		$usuario = Usuario::obtenerUsuario($nombreUsuario);
		if (password_verify($clave, $usuario->clave)) {
			$usuario->clave = null;
			$output = AutentificadorJWT::crearToken($usuario);
			
			$log = new Log();
			$log->idUsuarioCreador = $usuario->id;
			$log->descripcion = Log::obtenerDescripcionLogLogin();
			$log->guardarLog();
		}

		$payload = json_encode(array("respuesta" => $output));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function ObtenerOperacionesPorUsuario($request, $response, $args){
		$parametros = $request->getParsedBody();
		$nombreUsuario = $parametros['nombreUsuario'];
		$usuario = Usuario::obtenerUsuario($nombreUsuario);

		$operaciones = Log::obtenerLogsPorusuario($usuario->id);
		

		$payload = json_encode(array("listaOperaciones" => $operaciones));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function ObtenerOperacionesPorSector($request, $response, $args){
		$parametros = $request->getParsedBody();
		$sector = $parametros['sector'];

		$operaciones = Log::obtenerLogsPorSector($sector);
		
		
		$payload = json_encode(array("listaOperaciones" => $operaciones));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function traerRoles($request, $response, $args)
	{
		$lista = Rol::obtenerRoles();
		$payload = json_encode(array("listaRoles" => $lista));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}
}
