<?php

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
		}

        $consulta->bindValue(':idEntidad', $this->idEntidad, PDO::PARAM_STR);
        $consulta->bindValue(':descripcion', $this->descripcion);
        $consulta->bindValue(':nombre', $this->usuarioCreador);
        $consulta->execute();
		
        return $objAccesoDatos->obtenerUltimoId();
    }
	


	public static function getEstadoDefaultMesa(){
		return 'Libre';
	}

	public static function getEstadoDefaultUsuario(){
		return 'Libre';
	}

	public static function getEstadoDefaultPedido(){
		return 'Pendiente';
	}
}