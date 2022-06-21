<?php
require_once './models/Pedido.php';
require_once './models/ProductoPedido.php';
require_once 'ArchivoController.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
	public function CargarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();
		
		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);
		$nombreUsuario = AutentificadorJWT::ObtenerData($token)->nombre;

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
		$idPedido = $pedido->CrearPedidoDB();

		$estadoPedido = new Estado();
		$estadoPedido->idEntidad = $idPedido;
		$estadoPedido->descripcion = Estado::getEstadoDefaultPedido();
		$estadoPedido->usuarioCreador = $nombreUsuario;
		$estadoPedido->entidad = 'Pedido';

		$estadoPedido->guardarEstado();

		$estadoMesa = new Estado();
		$estadoMesa->idEntidad = MesaController::obtenerMesa($codigoMesa)->id;
		$estadoMesa->descripcion = STATUS_MESA_ESPERANDO;
		$estadoMesa->usuarioCreador = $nombreUsuario;
		$estadoMesa->entidad = 'Mesa';

		$estadoMesa->guardarEstado();

		
		// Se guardan los productos del pedido
		PedidoController::GuardarProductosDePedido($nombreUsuario, $codigo, $idProductos, $productos_cantidad);

		$payload = json_encode(array("mensaje" => "Pedido creado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}


	public function TraerUno($request, $response, $args)
	{
		$codigoPedido = $args['codigo'];
		$pedido = Pedido::ObtenerPedido($codigoPedido);

		$payload = json_encode($pedido);

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function TraerTodos($request, $response, $args)
	{
		$lista = Pedido::ObtenerTodos();

		$payload = json_encode(array("listaPedido" => $lista));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function TraerPendientes($request, $response, $args)
	{
		$lista = Pedido::ObtenerPedidosPendientesDB();

		foreach ($lista as $pedido) {
			$pedido->productosPedidos = ProductoPedido::obtenerProductosDePedido($pedido->codigo);
			foreach ($pedido->productosPedidos as $producto) {
				$producto->producto = Producto::obtenerProducto($producto->idProducto);
			}

			$pedido->tiempoEstimado = max(array_map(function($productoPedido) {
				return $productoPedido->tiempoEstimado;
			}, $pedido->productosPedidos));
	
		}

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

	public static function GuardarProductosDePedido($usuario, $codPedido, $IdProductos, $cantidad){
		for ($i=0; $i < count($IdProductos); $i++) { 
			$ped_prod = new ProductoPedido();
			$ped_prod->codigoPedido = $codPedido;
			$ped_prod->idProducto = $IdProductos[$i];
			$ped_prod->cantidad = $cantidad[$i];

			$id = $ped_prod->CargarProductoPedido();

			$estado_ped_prod = new Estado();
			$estado_ped_prod->idEntidad = $id;
			$estado_ped_prod->descripcion = Estado::getEstadoDefaultPedido();
			$estado_ped_prod->entidad = 'ProductoPedido';
			$estado_ped_prod->usuarioCreador = $usuario;
			$estado_ped_prod->guardarEstado();
		}
	}

	public static function TomarPedido($request, $response, $args){
		$mensaje = 'Ha habido un error';
		$parametros = $request->getParsedBody();

		$codigoPedido = $parametros['codigo'];
		$idProductoPedido = $parametros['idProductoPedido'];
		$tiempoEstimado = $parametros['tiempoEstimado'];
		
		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);
		$usuario = AutentificadorJWT::ObtenerData($token);

		$productoPedido = ProductoPedido::obtenerProductoPedido($idProductoPedido);
		$productoPedido->producto = Producto::obtenerProducto($productoPedido->idProducto);
		if ($usuario->rol == 'socio' || $productoPedido->producto->rolEncargado == $usuario->rol) {
			$pedido = Pedido::ObtenerPedidoDB($codigoPedido);

			$estado_pedido = new Estado();
			$estado_pedido->idEntidad = $pedido->id;
			$estado_pedido->descripcion = STATUS_PEDIDO_EN_PREPARACION;
			$estado_pedido->entidad = 'Pedido';
			$estado_pedido->usuarioCreador = $usuario->nombre;
			$estado_pedido->guardarEstado();

			$estado_ped_prod = new Estado();
			$estado_ped_prod->idEntidad = $idProductoPedido;
			$estado_ped_prod->descripcion = STATUS_PEDIDO_EN_PREPARACION;
			$estado_ped_prod->entidad = 'ProductoPedido';
			$estado_ped_prod->usuarioCreador = $usuario->nombre;
			$estado_ped_prod->guardarEstado();

			$productoPedido->tiempoEstimado = $tiempoEstimado;
			ProductoPedido::ModificarProductoPedido($productoPedido);

			$usuario = Usuario::obtenerUsuario($usuario->nombre);
			$estado_ped_prod = new Estado();
			$estado_ped_prod->idEntidad = $usuario->id;
			$estado_ped_prod->descripcion = STATUS_USUARIO_OCUPADO;
			$estado_ped_prod->entidad = 'Usuario';
			$estado_ped_prod->usuarioCreador = $usuario->nombre;
			$estado_ped_prod->guardarEstado();

			$mensaje = 'El pedido está ahora en preparación';
		}

		$payload = json_encode(array("mensaje" => $mensaje));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public static function TerminarPreparacionProducto($request, $response, $args){
		$mensaje = 'Ha habido un error';
		$parametros = $request->getParsedBody();

		$codigoPedido = $parametros['codigo'];
		$idProductoPedido = $parametros['idProductoPedido'];
		
		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);
		$usuario = AutentificadorJWT::ObtenerData($token);

		$productoPedido = ProductoPedido::obtenerProductoPedido($idProductoPedido);
		$productoPedido->producto = Producto::obtenerProducto($productoPedido->idProducto);
		if ($usuario->rol == 'socio' || $productoPedido->producto->rolEncargado == $usuario->rol) {
			if ($productoPedido->estado == STATUS_PEDIDO_EN_PREPARACION) {

				$estado_ped_prod = new Estado();
				$estado_ped_prod->idEntidad = $idProductoPedido;
				$estado_ped_prod->descripcion = STATUS_PRODUCTO_LISTO;
				$estado_ped_prod->entidad = 'ProductoPedido';
				$estado_ped_prod->usuarioCreador = $usuario->nombre;
				$estado_ped_prod->guardarEstado();


				$usuario = Usuario::obtenerUsuario($usuario->nombre);
				$estado_ped_prod = new Estado();
				$estado_ped_prod->idEntidad = $usuario->id;
				$estado_ped_prod->descripcion = STATUS_USUARIO_DEFAULT;
				$estado_ped_prod->entidad = 'Usuario';
				$estado_ped_prod->usuarioCreador = $usuario->nombre;
				$estado_ped_prod->guardarEstado();


				$pedido = Pedido::ObtenerPedido($codigoPedido);

				if ($pedido->estaListo()) {
					$estado_pedido = new Estado();
					$estado_pedido->idEntidad = $pedido->id;
					$estado_pedido->descripcion = STATUS_PEDIDO_LISTO_PARA_SERVIR;
					$estado_pedido->entidad = 'Pedido';
					$estado_pedido->usuarioCreador = $usuario->nombre;
					$estado_pedido->guardarEstado();

					$mensaje = 'Se completó el pedido ' . $pedido->codigo;
				}
				else {
					$mensaje = 'Se completó la preparación del producto ' . $productoPedido->producto->nombre;
				}
			} else {
				$mensaje = 'El producto no está en preparación';
			}
		}

		$payload = json_encode(array("mensaje" => $mensaje));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}
}