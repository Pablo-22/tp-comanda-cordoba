<?php

class Rol
{
    public $id;
    public $descripcion;

	public function guardarRol()
	{
		$objAccesoDatos = AccesoDatos::obtenerInstancia();
		$consulta = $objAccesoDatos->prepararConsulta("
			INSERT INTO roles (descripcion) 
			VALUES (
				:descripcion
			)
		");

		$consulta->bindValue(':descripcion', $this->descripcion);
		$consulta->execute();

		return $objAccesoDatos->obtenerUltimoId();
	}

	public static function obtenerRoles()
	{
		$objAccesoDatos = AccesoDatos::obtenerInstancia();
		$consulta = $objAccesoDatos->prepararConsulta("
			SELECT id, descripcion
			FROM roles
		");

		$consulta->execute();
		
		return $consulta->fetchAll(PDO::FETCH_CLASS, 'Rol');
	}
}