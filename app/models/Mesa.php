<?php

class Mesa
{
    public $id;
    public $codigo;
    public $capacidad;

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            INSERT INTO mesas (codigo, capacidad) 
            VALUES ( 
                :codigoMesa AS codigo,
                :capacidad AS capacidad
            )
            LIMIT 1
        ");

        $capacidadHash = password_hash($this->capacidad, PASSWORD_DEFAULT);
        $consulta->bindValue(':codigoMesa', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':capacidad', $capacidadHash);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT U.id, 
                U.codigo, 
                U.capacidad, 
            FROM mesas U
        ");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerMesa($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT U.id, 
                U.codigo, 
                U.capacidad, 
            FROM mesas U
            WHERE U.codigo = :codigoMesa
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
            SET U.codigo = :mesa, 
                U.capacidad = :capacidad,
            FROM mesas U
            WHERE id = :id
        ");
        $consulta->bindValue(':mesa', $mesa->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':capacidad', $mesa->capacidad, PDO::PARAM_STR);
        $consulta->bindValue(':id', $mesa->id, PDO::PARAM_INT);
        $consulta->execute();
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
    }
}