<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
      $parametros = $request->getParsedBody();

      $id = $parametros['id'];
      $codigo = $parametros['codigo'];
      $codigoMesa = $parametros['codigoMesa'];
      $rutaImagen = $parametros['rutaImagen'];

      // Creamos el usuario
      $pedido = new Pedido();
      $pedido->id = $id;
      $pedido->codigo = $codigo;
      $pedido->codigoMesa = $codigoMesa;
      $pedido->rutaImagen = $rutaImagen;
      $pedido->crearPedido();
      
      $path = '../ImagenesPedidos/' . pedido->codigo . '.png';
      ArchivoController::SaveFile($path, true, 5000, ['.png', '.jpg', '.jpeg']);

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
}
