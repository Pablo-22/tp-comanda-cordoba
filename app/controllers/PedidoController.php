<?php
require_once './models/Pedido.php';
require_once './models/Pedido_Productos.php';
require_once 'ArchivoController.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
	public function CargarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();

		$codigo = $parametros['codigo'];
		$codigoMesa = $parametros['codigoMesa'];
		$productos_cantidad = $parametros['cantidad'];
		$idProductos = $parametros['producto'];

		// Creamos el usuario 
		$pedido = new Pedido();
		$pedido->codigo = $codigo;
		$pedido->codigoMesa = $codigoMesa;

		$path = '..\\ImagenesPedidos\\' . $pedido->codigo . '.png';
		ArchivoController::SaveFile($path, true, 500000, ['.png', '.jpg', '.jpeg']);
		
		$pedido->rutaImagen = $path;
		$pedido->crearPedido();

		PedidoController::GuardarProductosDePedido($codigo, $idProductos, $productos_cantidad);
		

		$payload = json_encode(array("mensaje" => "Pedido creado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}


	public function TraerUno($request, $response, $args)
	{
		// Buscamos usuario por codigo
		$pedido = $args['codigo'];
		$usuario = Pedido::obtenerPedido($pedido);
		$payload = json_encode($usuario);

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function TraerTodos($request, $response, $args)
	{
		$lista = Pedido::obtenerTodos();
		$payload = json_encode(array("listaPedido" => $lista));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}
	
	public function ModificarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();

		$id = $parametros['id'];
		Pedido::modificarPedido($id);

		$payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function BorrarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();

		$usuarioId = $parametros['id'];
		Pedido::borrarPedido($id);

		$payload = json_encode(array("mensaje" => "Pedido borrado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public static function GuardarProductosDePedido($codPedido, $IdProductos, $cantidad){
		for ($i=0; $i < count($IdProductos); $i++) { 
			$ped_prod = new PedidoProductos();
			$ped_prod->codigoPedido = $codPedido;
			$ped_prod->idProducto = $IdProductos[$i];
			$ped_prod->cantidad = $cantidad[$i];

			$ped_prod->CargarPedido_Productos();
		}
	}
}
