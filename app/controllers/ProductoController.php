<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
	public function cargarUno($request, $response, $args)
	{
		$mensaje = 'Ha habido un error';
		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);
		$usuario = AutentificadorJWT::obtenerData($token);


		$parametros = $request->getParsedBody();

		$nombre = $parametros['nombre'];
		$tiempoEstimado = $parametros['tiempoEstimado'];
		$precio = $parametros['precio'];
		$rolEncargado = $parametros['rolEncargado'];

		if (Producto::obtenerProducto($nombre)) {
			$mensaje = 'Ya existe un producto con ese nombre';
		} else {
			// Creamos el producto
			$producto = new Producto();
			$producto->nombre = $nombre;
			$producto->tiempoEstimado = $tiempoEstimado;
			$producto->precio = $precio;
			$producto->rolEncargado = $rolEncargado;
			$idProducto = $producto->crearProducto();

			$log = new Log();
			$log->idUsuarioCreador = $usuario->id;
			$log->descripcion = Log::obtenerDescripcionLogCargarProducto($producto->nombre);
			$log->guardarLog();

			$mensaje = 'Producto creado con éxito';
		}		

		$payload = json_encode(array("mensaje" => $mensaje));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}


	public function traerUno($request, $response, $args)
	{
		// Buscamos producto por nombre
		$prd = $args['nombre'];
		$producto = Producto::obtenerProducto($prd);
		$payload = json_encode($producto);

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function traerTodos($request, $response, $args)
	{
		$lista = Producto::obtenerTodos();
		$payload = json_encode(array("listaProducto" => $lista));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}
	
	public function modificarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();

		$id = isset($parametros['id']) ? $parametros['id'] : null;
		$nombre = isset($parametros['nombre']) ? $parametros['nombre'] : null;
		$tiempoEstimado = isset($parametros['tiempoEstimado']) ? $parametros['tiempoEstimado'] : null;
		$precio = isset($parametros['precio']) ? $parametros['precio'] : null;
		$rolEncargado = isset($parametros['rolEncargado']) ? $parametros['rolEncargado'] : null;

		if ($id && $nombre && $tiempoEstimado && $precio && $rolEncargado) {
			$producto = new Producto();
			$producto->id = $id;
			$producto->nombre = $nombre;
			$producto->tiempoEstimado = $tiempoEstimado;
			$producto->precio = $precio;
			$producto->rolEncargado = $rolEncargado;
			$producto->modificarProducto();
		} else {
			$mensaje = 'No se pudo modificar el producto. Faltan parámetros';
			$response->getBody()->write($mensaje);
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus(400);
		}

		$payload = json_encode(array("mensaje" => "Producto modificado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function borrarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();

		$productoId = $parametros['id'];
		Producto::borrarProducto($id);

		$payload = json_encode(array("mensaje" => "Producto borrado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function ImportarCSV($request, $response, $args){
		$path = '..\\tmpCSV\\' . date("d-m-y His") . '.csv';
		$mensaje = ArchivoController::guardarArchivo($path, true, 500000, ['.csv']);

		$data = ArchivoController::ReadCsv($path);

		$arrayProductos = array();

		foreach ($data as $item) {
			$prd = new Producto();
			$prd->nombre = $item[0];
			$prd->tiempoEstimado = $item[1];
			$prd->precio = $item[2];
			$prd->rolEncargado = $item[3];

			array_push($arrayProductos, $prd);
		}

		Producto::crearProductosDB($arrayProductos);

		$payload = json_encode(array("mensaje" => $mensaje));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}
}
