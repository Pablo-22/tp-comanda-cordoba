<?php

class Mesa
{
    public $id;
    public $codigo;
    public $capacidad;
    public $estado;

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            INSERT INTO mesas (codigo, capacidad) 
            VALUES ( 
                :codigoMesa,
                :capacidad
            )
        ");

        $consulta->bindValue(':codigoMesa', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':capacidad', $this->capacidad);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT  M.id, 
                M.codigo, 
                M.capacidad,
				E.descripcion AS estado
            FROM mesas M
				
				LEFT JOIN ( -- Obtener el último estado
					SELECT EP.idEntidad AS idMesa, EP.descripcion
					FROM estados_mesas EP
						JOIN (
							SELECT id, 
								idEntidad AS idMesa, 
								MAX(fechaInsercion) AS fechaInsercion
							FROM estados_mesas
							GROUP BY idEntidad
						) EP2 ON EP2.idMesa = EP.idEntidad 
								AND EP2.fechaInsercion = EP.fechaInsercion
				) E ON E.idMesa = M.id
        ");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerMesa($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT M.id, 
                M.codigo, 
                M.capacidad
            FROM mesas M
            WHERE M.codigo = :codigoMesa
        ");
        $consulta->bindValue(':codigoMesa', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function modificarMesa($mesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("
            UPDATE mesas 
            SET M.codigo = :mesa, 
                M.capacidad = :capacidad
            FROM mesas M
            WHERE id = :id
        ");
        $consulta->bindValue(':mesa', $mesa->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':capacidad', $mesa->capacidad, PDO::PARAM_STR);
        $consulta->bindValue(':id', $mesa->id, PDO::PARAM_INT);
        $consulta->execute();

		return $objAccesoDato->obtenerUltimoId();
    }

    public static function borrarMesa($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("
            UPDATE mesas SET fechaBaja = :fechaBaja WHERE id = :id
        ");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();

		return $objAccesoDato->obtenerUltimoId();
    }
}