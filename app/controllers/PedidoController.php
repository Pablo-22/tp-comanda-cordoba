<?php
require_once './models/Pedido.php';
require_once './models/ProductoPedido.php';
require_once 'ArchivoController.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
	public function cargarUno($request, $response, $args)
	{
		$parametros = $request->getParsedBody();
		
		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);
		$usuario = AutentificadorJWT::obtenerData($token);

		$codigo = $parametros['codigo'];
		$codigoMesa = $parametros['codigoMesa'];
		$productos_cantidad = $parametros['cantidad'];
		$idProductos = $parametros['producto'];
		$nombreCliente = $parametros['nombreCliente'];

		$pedido = new Pedido();
		$pedido->codigo = $codigo;
		$pedido->codigoMesa = $codigoMesa;
		$pedido->nombreCliente = $nombreCliente;

		if (isset($_FILES['archivo']['name'])) {
			$path = '..\\ImagenesPedidos\\' . $pedido->codigo . '.png';
			ArchivoController::guardarArchivo($path, true, 500000, ['.png', '.jpg', '.jpeg']);
	
			$pedido->rutaImagen = $path;
		}
		$idPedido = $pedido->crearPedidoDB();

		$estadoPedido = new Estado();
		$estadoPedido->idEntidad = $idPedido;
		$estadoPedido->descripcion = Estado::getEstadoDefaultPedido();
		$estadoPedido->usuarioCreador = $usuario->nombre;
		$estadoPedido->entidad = 'Pedido';

		$estadoPedido->guardarEstado();

		$estadoMesa = new Estado();
		$estadoMesa->idEntidad = MesaController::obtenerMesa($codigoMesa)->id;
		$estadoMesa->descripcion = STATUS_MESA_ESPERANDO;
		$estadoMesa->usuarioCreador = $usuario->nombre;
		$estadoMesa->entidad = 'Mesa';

		$estadoMesa->guardarEstado();

		
		// Se guardan los productos del pedido
		PedidoController::guardarProductosDePedido($usuario->nombre, $codigo, $idProductos, $productos_cantidad);

		$log = new Log();
		$log->idUsuarioCreador = $usuario->id;
		$log->descripcion = Log::obtenerDescripcionLogCrearPedido();
		$log->guardarLog();

		$payload = json_encode(array("mensaje" => "Pedido creado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function cargarFotoPedido(){
		$mensaje = 'Ha habido un error';
		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);
		$usuario = AutentificadorJWT::obtenerData($token);

		if ($usuario->rol == 'socio' || $usuario->rol == 'mozo') {
			$pedido = Pedido::obtenerPedido();
			$path = '..\\ImagenesPedidos\\' . $pedido->codigo . '.png';
			ArchivoController::guardarArchivo($path, true, 500000, ['.png', '.jpg', '.jpeg']);

			$pedido->rutaImagen = $path;

			$pedido->modificarPedidoDB();
		}else {
			$mensaje = 'Acceso denegado o permisos insuficientes';
		}

		$log = new Log();
		$log->idUsuarioCreador = $usuario->id;
		$log->descripcion = Log::obtenerDescripcionLogCrearPedido();
		$log->guardarLog();

		$payload = json_encode(array("mensaje" => "Pedido creado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}


	public function traerUno($request, $response, $args)
	{

		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);
		$nombreUsuario = AutentificadorJWT::obtenerData($token)->nombre;

		$codigoPedido = $args['codigo'];
		$pedido = Pedido::obtenerPedido($codigoPedido);

		$payload = json_encode($pedido);

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function traerTodos($request, $response, $args)
	{
		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);
		$nombreUsuario = AutentificadorJWT::obtenerData($token)->nombre;

		$lista = Pedido::obtenerTodos();

		$payload = json_encode(array("listaPedido" => $lista));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function traerPendientes($request, $response, $args)
	{
		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);
		$nombreUsuario = AutentificadorJWT::obtenerData($token)->nombre;

		$lista = Pedido::obtenerPedidosPendientesDB();

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
	
	public function modificarUno($request, $response, $args)
	{
		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);
		$usuario = AutentificadorJWT::obtenerData($token);
		
		$parametros = $request->getParsedBody();

		$id = $parametros['id'];
		Pedido::modificarPedido($id);

		$payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public function borrarUno($request, $response, $args)
	{
		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);
		$nombreUsuario = AutentificadorJWT::obtenerData($token)->nombre;

		$parametros = $request->getParsedBody();

		$usuarioId = $parametros['id'];
		Pedido::borrarPedidoDB($id);

		$payload = json_encode(array("mensaje" => "Pedido borrado con exito"));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public static function guardarProductosDePedido($usuario, $codPedido, $IdProductos, $cantidad){
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

	public static function tomarPedido($request, $response, $args){
		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);
		$usuario = AutentificadorJWT::obtenerData($token);

		$mensaje = 'Ha habido un error';
		$parametros = $request->getParsedBody();

		$codigoPedido = $parametros['codigo'];
		$idProductoPedido = $parametros['idProductoPedido'];
		$tiempoEstimado = $parametros['tiempoEstimado'];

		$productoPedido = ProductoPedido::obtenerProductoPedido($idProductoPedido);
		$productoPedido->producto = Producto::obtenerProducto($productoPedido->idProducto);
		if ($usuario->rol == 'socio' || $productoPedido->producto->rolEncargado == $usuario->rol) {
			$pedido = Pedido::obtenerPedidoDB($codigoPedido);

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
			ProductoPedido::modificarProductoPedido($productoPedido);

			$usuario = Usuario::obtenerUsuario($usuario->nombre);
			$estado_ped_prod = new Estado();
			$estado_ped_prod->idEntidad = $usuario->id;
			$estado_ped_prod->descripcion = STATUS_USUARIO_OCUPADO;
			$estado_ped_prod->entidad = 'Usuario';
			$estado_ped_prod->usuarioCreador = $usuario->nombre;
			$estado_ped_prod->guardarEstado();

			$log = new Log();
			$log->idUsuarioCreador = $usuario->id;
			$log->descripcion = Log::obtenerDescripcionLogtomarPedido($pedido->codigo);
			$log->guardarLog();

			$mensaje = 'El pedido está ahora en preparación';
		}

		$payload = json_encode(array("mensaje" => $mensaje));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

	public static function terminarPreparacionProducto($request, $response, $args){

		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);
		$usuario = AutentificadorJWT::obtenerData($token);

		$mensaje = 'Ha habido un error';
		$parametros = $request->getParsedBody();

		$codigoPedido = $parametros['codigo'];
		$idProductoPedido = $parametros['idProductoPedido'];

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
				$estado_usuario = new Estado();
				$estado_usuario->idEntidad = $usuario->id;
				$estado_usuario->descripcion = STATUS_USUARIO_DEFAULT;
				$estado_usuario->entidad = 'Usuario';
				$estado_usuario->usuarioCreador = $usuario->nombre;
				$estado_usuario->guardarEstado();


				$pedido = Pedido::obtenerPedido($codigoPedido);

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


	public static function entregarPedido($request, $response, $args){

		$mensaje = 'Ha habido un error';
		$parametros = $request->getParsedBody();

		$codigoPedido = $parametros['codigo'];
		
		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);
		$usuario = AutentificadorJWT::obtenerData($token);

		$pedido = Pedido::obtenerPedido($codigoPedido);
		$mesa = Mesa::obtenerMesa($pedido->codigoMesa);
		if ($usuario->rol == 'socio' || $usuario->rol == 'mozo') {
			if ($pedido->estado == STATUS_PEDIDO_LISTO_PARA_SERVIR) {

				$estado_pedido = new Estado();
				$estado_pedido->idEntidad = $pedido->id;
				$estado_pedido->descripcion = STATUS_PEDIDO_ENTREGADO;
				$estado_pedido->entidad = 'Pedido';
				$estado_pedido->usuarioCreador = $usuario->nombre;
				$estado_pedido->guardarEstado();

				$estado_mesa = new Estado();
				$estado_mesa->idEntidad = $mesa->id;
				$estado_mesa->descripcion = STATUS_MESA_COMIENDO;
				$estado_mesa->entidad = 'Mesa';
				$estado_mesa->usuarioCreador = $usuario->nombre;
				$estado_mesa->guardarEstado();

				$mensaje = 'Se entregó el pedido ' . $pedido->codigo;
			} else {
				$mensaje = 'El pedido no está listo para servir';
			}
		} else {
			$mensaje = 'Usuario no autorizado';
		}

		$payload = json_encode(array("mensaje" => $mensaje));

		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}



	public static function cobrarPedido($request, $response, $args){

		$mensaje = 'Ha habido un error';
		$parametros = $request->getParsedBody();

		$codigoPedido = $parametros['codigo'];
		
		$token = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $token)[1]);
		$usuario = AutentificadorJWT::obtenerData($token);

		$pedido = Pedido::obtenerPedido($codigoPedido);
		$mesa = Mesa::obtenerMesa($pedido->codigoMesa);
		if ($usuario->rol == 'socio' || $usuario->rol == 'mozo') {
			if ($pedido->estado == STATUS_PEDIDO_ENTREGADO) {

				$estado_pedido = new Estado();
				$estado_pedido->idEntidad = $pedido->id;
				$estado_pedido->descripcion = STATUS_PEDIDO_PAGADO;
				$estado_pedido->entidad = 'Pedido';
				$estado_pedido->usuarioCreador = $usuario->nombre;
				$estado_pedido->guardarEstado();

				$estado_mesa = new Estado();
				$estado_mesa->idEntidad = $mesa->id;
				$estado_mesa->descripcion = STATUS_MESA_PAGANDO;
				$estado_mesa->entidad = 'Mesa';
				$estado_mesa->usuarioCreador = $usuario->nombre;
				$estado_mesa->guardarEstado();

				$mensaje = 'Se pagó el pedido ' . $pedido->codigo;
			} else {
				$mensaje = 'El pedido no fue entregado';
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