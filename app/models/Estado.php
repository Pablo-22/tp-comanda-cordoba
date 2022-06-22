<?php

define("STATUS_MESA_DEFAULT", "Libre");
define("STATUS_MESA_ESPERANDO", "Con cliente esperando pedido");
define("STATUS_MESA_COMIENDO", "Con cliente comiendo pedido");
define("STATUS_MESA_PAGANDO", "Con cliente pagando pedido");
define("STATUS_MESA_CERRADA", "Cerrada");

define("STATUS_PEDIDO_DEFAULT", "Pendiente");
define("STATUS_PEDIDO_EN_PREPARACION", "En preparación");
define("STATUS_PEDIDO_LISTO_PARA_SERVIR", "Listo para servir");
define("STATUS_PEDIDO_ENTREGADO", "Pedido entregado");
define("STATUS_PEDIDO_PAGADO", "Pedido pagado");


define("STATUS_PRODUCTO_LISTO", "Listo");


define("STATUS_USUARIO_DEFAULT", "Libre");
define("STATUS_USUARIO_OCUPADO", "Ocupado");


class Estado
{
    public $id;
    public $descripcion;
    public $usuarioCreador;
    public $idEntidad;
	public $entidad;

    public function guardarEstado()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
		$consulta;
		switch ($this->entidad) {
			case 'Usuario':
				$consulta = $objAccesoDatos->prepararConsulta("
					INSERT INTO estados_usuarios (descripcion, idUsuarioCreador, idEntidad) 
					VALUES (
						:descripcion,
						(SELECT U.id FROM usuarios U WHERE U.nombre = :nombre),
						:idEntidad
					)
				");
				break;
			case 'Mesa':
				$consulta = $objAccesoDatos->prepararConsulta("
					INSERT INTO estados_mesas (descripcion, idUsuarioCreador, idEntidad) 
					VALUES (
						:descripcion,
						(SELECT U.id FROM usuarios U WHERE U.nombre = :nombre),
						:idEntidad
					)
				");
				break;
			case 'Pedido':
				$consulta = $objAccesoDatos->prepararConsulta("
					INSERT INTO estados_pedidos (descripcion, idUsuarioCreador, idEntidad) 
					VALUES (
						:descripcion,
						(SELECT U.id FROM usuarios U WHERE U.nombre = :nombre),
						:idEntidad
					)
				");
				break;
			case 'ProductoPedido':
				$consulta = $objAccesoDatos->prepararConsulta("
					INSERT INTO estados_productos_pedidos (descripcion, idUsuarioCreador, idEntidad) 
					VALUES (
						:descripcion,
						(SELECT U.id FROM usuarios U WHERE U.nombre = :nombre),
						:idEntidad
					)
				");
				break;
		}
        $consulta->bindValue(':idEntidad', $this->idEntidad, PDO::PARAM_INT);
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->usuarioCreador, PDO::PARAM_STR);
        $consulta->execute();
		
        return $objAccesoDatos->obtenerUltimoId();
    }
	
	
	// MESAS
	public static function getEstadoDefaultMesa(){
		return 'Libre';
	}

	// USUARIOS
	public static function getEstadoDefaultUsuario(){
		return 'Libre';
	}

	// PEDIDOS
	public static function getEstadoDefaultPedido(){
		return 'Pendiente';
	}
}