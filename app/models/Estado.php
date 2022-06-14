<?php

class Estado
{
    public $id;
    public $descripcion;
    public $usuarioCreador;
    public $idEntidad;

    public function guardarEstadoMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            INSERT INTO estados_mesas (descripcion, idUsuarioCreador, idEntidad) 
			:descripcion,
			(SELECT U.id FROM usuarios U WHERE U.nombre = :nombre),
			:idEntidad
        ");

        $consulta->bindValue(':idEntidad', $this->idEntidad, PDO::PARAM_STR);
        $consulta->bindValue(':capacidad', $this->descripcion);
        $consulta->bindValue(':nombre', $this->usuarioCreador);
        $consulta->execute();
		
        return $objAccesoDatos->obtenerUltimoId();
    }


	public static function getEstadoDefaultMesa(){
		return 'Libre';
	}
}