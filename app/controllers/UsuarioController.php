<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';
require_once './middlewares/AutentificadorJWT.php';

class UsuarioController extends Usuario implements IApiUsable
{
	public function CargarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();

		$nombre = $parametros['nombre'];
		$clave = $parametros['clave'];
		$rol = $parametros['rol'];

		// Creamos el usuario
		$usr = new Usuario();
		$usr->nombre = $nombre;
		$usr->clave = $clave;
		$usr->rol = $rol;
		$usr->crearUsuario();

		$estadoMesa = new Estado();
		$estadoMesa->idEntidad = $idMesa;
		$estadoMesa->Descripcion = Estado::getEstadoDefaultMesa();
		$estadoMesa->usuarioCreador = $nombreUsuario;

		$payload = json_encode(array("mensaje" => "Usuario creado con exito"));

		$response->getBody()->write($payload);
		return $response
		->withHeader('Content-Type', 'application/json');
	}


	public function TraerUno($request, $response, $args)
	{
		// Buscamos usuario por nombre
		$usr = $args['nombre'];
		$usuario = Usuario::obtenerUsuario($usr);
		$payload = json_encode($usuario);

		$response->getBody()->write($payload);
		return $response
		->withHeader('Content-Type', 'application/json');
	}

	public function TraerTodos($request, $response, $args)
	{
		$lista = Usuario::obtenerTodos();
		$payload = json_encode(array("listaUsuario" => $lista));

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

		// Creamos el usuario
		$usr = new Usuario();
		$usr->nombre = $nombre;
		$usr->clave = $clave;
		$usr->rol = $rol;
		$usr->id = $id;

		$usr->modificarUsuario();


		$payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function BorrarUno($request, $response, $args)
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

		$usuario = Usuario::obtenerUsuario($nombreUsuario, $clave);
		if (password_verify($clave, $usuario->clave)) {
			$usuario->clave = null;
			$output = AutentificadorJWT::crearToken($usuario);
		}

		$payload = json_encode(array("respuesta" => $output));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}
}
