<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
      $parametros = $request->getParsedBody();

      $id = $parametros['id'];
      $codigo = $parametros['codigo'];
      $capacidad = $parametros['capacidad'];

      // Creamos la mesa
      $mesa = new Mesa();
      $mesa->id = $id;
      $mesa->codigo = $codigo;
      $mesa->capacidad = $capacidad;
      $mesa->crearMesa();

      $payload = json_encode(array("mensaje" => "Mesa creado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }


  public function TraerUno($request, $response, $args)
  {
      // Buscamos mesa por codigo
      $mesa = $args['codigo'];
      $mesa = Mesa::obtenerMesa($mesa);
      $payload = json_encode($mesa);

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
      $lista = Mesa::obtenerTodos();
      $payload = json_encode(array("listaMesa" => $lista));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }
  
  public function ModificarUno($request, $response, $args)
  {
      $parametros = $request->getParsedBody();

      $id = $parametros['id'];
      Mesa::modificarMesa($id);

      $payload = json_encode(array("mensaje" => "Mesa modificado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
      $parametros = $request->getParsedBody();

      $mesaId = $parametros['id'];
      Mesa::borrarMesa($id);

      $payload = json_encode(array("mensaje" => "Mesa borrado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }
}