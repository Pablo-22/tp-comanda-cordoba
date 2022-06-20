<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
	public function CargarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();

		$nombre = $parametros['nombre'];
		$tiempoEstimado = $parametros['tiempoEstimado'];
		$precio = $parametros['precio'];
		$rolEncargado = $parametros['rolEncargado'];

		// Creamos el producto
		$prd = new Producto();
		$prd->nombre = $nombre;
		$prd->tiempoEstimado = $tiempoEstimado;
		$prd->precio = $precio;
		$prd->rolEncargado = $rolEncargado;
		$prd->crearProducto();

		$payload = json_encode(array("mensaje" => "Producto creado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}


	public function TraerUno($request, $response, $args)
	{
		// Buscamos producto por nombre
		$prd = $args['nombre'];
		$producto = Producto::obtenerProducto($prd);
		$payload = json_encode($producto);

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function TraerTodos($request, $response, $args)
	{
		$lista = Producto::obtenerTodos();
		$payload = json_encode(array("listaProducto" => $lista));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}
	
	public function ModificarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();

		$id = $parametros['id'];
		Producto::modificarProducto($id);

		$payload = json_encode(array("mensaje" => "Producto modificado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function BorrarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();

		$productoId = $parametros['id'];
		Producto::borrarProducto($id);

		$payload = json_encode(array("mensaje" => "Producto borrado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}
}
