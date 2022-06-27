<?php

class Log
{
    public $id;
    public $descripcion;
    public $idUsuarioCreador;
    public $nombreUsuario;
	public $fechaCreacion;

    public function guardarLog()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
		$consulta = $objAccesoDatos->prepararConsulta("
			INSERT INTO logs (descripcion, idUsuarioCreador) 
			VALUES (
				:descripcion,
				:idUsuarioCreador
			)
		");

        $consulta->bindValue(':descripcion', $this->descripcion);
        $consulta->bindValue(':idUsuarioCreador', $this->idUsuarioCreador);
        $consulta->execute();
		
        return $objAccesoDatos->obtenerUltimoId();
    }

	public static function obtenerLogsPorUsuario($idUsuario)
	{
		$objAccesoDatos = AccesoDatos::obtenerInstancia();
		$consulta = $objAccesoDatos->prepararConsulta("
			SELECT L.id, L.descripcion, L.idUsuarioCreador, U.nombre AS nombreUsuario, L.fechaCreacion
			FROM logs L
				JOIN usuarios U ON U.id = L.idUsuarioCreador
			WHERE L.idUsuarioCreador = :idUsuario
		");

		$consulta->bindValue(':idUsuario', $idUsuario);
		$consulta->execute();
		
		return $consulta->fetchAll(PDO::FETCH_CLASS, 'Log');
	}

	public static function obtenerLogsPorSector($sector)
	{
		$objAccesoDatos = AccesoDatos::obtenerInstancia();
		$consulta = $objAccesoDatos->prepararConsulta("
			SELECT L.id, L.descripcion, L.idUsuarioCreador, U.nombre AS nombreUsuario ,L.fechaCreacion
			FROM logs L
				JOIN usuarios U ON U.id = L.idUsuarioCreador
			WHERE U.sector = :sector
		");

		$consulta->bindValue(':sector', $sector);
		$consulta->execute();
		
		return $consulta->fetchAll(PDO::FETCH_CLASS, 'Log');
	}




	public static function obtenerDescripcionLogLogin(){
		return 'Ha iniciado sesión';
	}

	public static function obtenerDescripcionLogCrearPedido(){
		return 'Ha creado un pedido';
	}

	public static function obtenerDescripcionLogCargarProducto(){
		return 'Ha cargado un producto';
	}

	public static function obtenerDescripcionLogCargarPedido(){
		return 'Ha cargado un pedido';
	}

	public static function obtenerDescripcionLogtomarPedido($codPedido){
		return 'Ha tomado el pedido ' . $codPedido;
	}

	public static function obtenerDescripcionLogTerminarPedido($codPedido){
		return 'Ha terminado la preparación del producto ' . $codPedido;
	}
	
	public static function obtenerDescripcionLogentregarPedido($codPedido){
		return 'Ha entregado el pedido ' . $codPedido;
	}
	
	public static function obtenerDescripcionLogCerrarPedido($codPedido){
		return 'Ha cerrado el pedido ' . $codPedido;
	}

	public static function obtenerDescripcionLogCancelarPedido($codPedido){
		return 'Ha cancelado el pedido ' . $codPedido;
	}

	public static function obtenerDescripcionLogCambiarEstadoMesa($codMesa){
		return 'Ha cambiado el estado de la mesa ' . $codMesa;
	}

	public static function obtenerDescripcionLogCambiarEstadoUsuario($estado){
		return 'Ha cambiado su estado a ' . $estado;
	}

	public static function obtenerDescripcionLogCambiarProducto($producto){
		return 'Ha modificado el producto ' . $producto;
	}

	public static function obtenerDescripcionLogCambiarUsuario($usuario){
		return 'Ha modificado el usuario ' . $usuario;
	}

	public static function obtenerDescripcionLogCambiarPedido($pedido){
		return 'Ha modificado el pedido ' . $pedido;
	}

	public static function obtenerDescripcionLogCambiarMesa($mesa){
		return 'Ha modificado la mesa ' . $mesa;
	}

	public static function obtenerDescripcionLogCargarEncuesta($codigoPedido){
		return 'Se ha cargado la encuesta relacionada al pedido ' . $codigoPedido;
	}
}