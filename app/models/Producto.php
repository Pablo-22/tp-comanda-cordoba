<?php

class Producto
{
    public $id;
    public $nombre;
    public $tiempoEstimado;
    public $precio;
    public $rolEncargado;

    public function crearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            INSERT INTO productos (nombre, tiempoEstimado, precio, idRolEncargado) 
            SELECT 
                :nombreProducto AS nombre,
                :tiempoEstimado AS tiempoEstimado,
                :precio AS precio,
                R.id AS idRolEncargado
            FROM roles R
            WHERE R.descripcion = :rolEncargado
            LIMIT 1
        ");

        $consulta->bindValue(':nombreProducto', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoEstimado', $this->tiempoEstimado);
        $consulta->bindValue(':precio', $this->precio);
        $consulta->bindValue(':rolEncargado', $this->rolEncargado, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT P.id, 
                P.nombre, 
                P.tiempoEstimado, 
                P.precio, 
                R.descripcion as rolEncargado
            FROM productos P
                JOIN roles R ON R.id = P.idRolEncargado
        ");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function obtenerProducto($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT P.id, 
                P.nombre, 
                P.tiempoEstimado, 
                P.precio, 
                R.descripcion as rolEncargado
            FROM productos P
                JOIN roles R ON R.id = P.idRolEncargado
            WHERE P.id = :idProducto
        ");
        $consulta->bindValue(':idProducto', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }

    public static function modificarProducto($producto)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("
            UPDATE productos 
            SET P.nombre = :nombre, 
                P.tiempoEstimado = :tiempoEstimado, 
                P.precio = :precio, 
                P.idRolEncargado = R.id
            FROM productos P
                JOIN roles R ON R.id = :rolEncargado
            WHERE P.id = :id
        ");
        $consulta->bindValue(':producto', $producto->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoEstimado', $producto->tiempoEstimado);
        $consulta->bindValue(':precio', $producto->precio);
        $consulta->bindValue(':rolEncargado', $producto->rolEncargado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $producto->id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarProducto($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("
            UPDATE productos SET fechaBaja = :fechaBaja WHERE id = :id
        ");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }
}