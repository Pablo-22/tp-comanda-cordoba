<?php

class Usuario
{
    public $id;
    public $nombre;
    public $clave;
    public $rol;
	public $sector;
    public $estado;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            INSERT INTO usuarios (nombre, clave, idRol, sector) 
            SELECT 
                :nombreUsuario AS nombre,
                :clave AS clave,
                R.id AS idRol,
				:sector AS sector
            FROM roles R
            WHERE R.descripcion = :rol
            LIMIT 1
        ");

        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':nombreUsuario', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':rol', $this->rol, PDO::PARAM_STR);
		$consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT U.id, 
                U.nombre, 
                U.clave, 
                R.descripcion as rol,
				E.descripcion AS estado,
				U.sector
            FROM usuarios U
                JOIN roles R ON R.id = U.idRol

				LEFT JOIN ( -- Obtener el último estado
					SELECT EP.idEntidad AS idUsuario, EP.descripcion
					FROM estados_usuarios EP
						JOIN (
							SELECT id, 
								idEntidad AS idUsuario, 
								MAX(fechaInsercion) AS fechaInsercion
							FROM estados_usuarios
							GROUP BY idEntidad
						) EP2 ON EP2.idUsuario = EP.idEntidad 
								AND EP2.fechaInsercion = EP.fechaInsercion
				) E ON E.idUsuario = U.id
			WHERE U.fechaBaja IS NULL
		");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($nombre)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT U.id, 
                U.nombre, 
                U.clave, 
                R.descripcion as rol,
				E.descripcion AS estado,
				U.sector
			FROM usuarios U
                JOIN roles R ON R.id = U.idRol

				LEFT JOIN ( -- Obtener el último estado
					SELECT EP.idEntidad AS idUsuario, EP.descripcion
					FROM estados_usuarios EP
						JOIN (
							SELECT id, 
								idEntidad AS idUsuario, 
								MAX(fechaInsercion) AS fechaInsercion
							FROM estados_usuarios
							GROUP BY idEntidad
						) EP2 ON EP2.idUsuario = EP.idEntidad 
								AND EP2.fechaInsercion = EP.fechaInsercion
				) E ON E.idUsuario = U.id
            WHERE U.nombre = :nombreUsuario
				AND U.fechaBaja IS NULL
        ");
        $consulta->bindValue(':nombreUsuario', $nombre, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public function modificarUsuario()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("
            UPDATE usuarios  U
				JOIN roles R ON R.descripcion = :rol
            SET U.nombre = :usuario, 
                U.clave = :clave,
                U.idRol = R.id,
				U.sector = :sector
            WHERE U.id = :id
        ");
        $consulta->bindValue(':usuario', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':rol', $this->rol, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function borrarUsuario($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("
            UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id
        ");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }
}