<?php

class Pedido
{
    public $id;
    public $codigo;
    public $rutaImagen;
    public $codigoMesa;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            INSERT INTO pedidos (codigo, rutaImagen, idMesa) 
            SELECT 
                :codigoPedido AS codigo,
                :rutaImagen AS rutaImagen,
                M.id AS idMesa
            FROM mesas M
            WHERE M.codigo = :codigoMesa
            LIMIT 1
        ");

        $consulta->bindValue(':codigoPedido', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':rutaImagen', $ $this->rutaImagen);
        $consulta->bindValue(':codigoMesa', $this->rol, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT P.id, 
                P.codigo, 
                P.rutaImagen, 
                M.codigo as codigoMesa
            FROM pedidos M
                JOIN mesas R ON M.id = P.idMesa
        ");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT P.id, 
                P.codigo, 
                P.rutaImagen, 
                M.codigo as codigoMesa
            FROM pedidos M
                JOIN mesas R ON M.id = P.idMesa
            WHERE P.codigo = :codigoPedido
        ");
        $consulta->bindValue(':codigoPedido', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function modificarPedido($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("
            UPDATE pedidos 
            SET P.codigo = :codigo, 
                P.rutaImagen = :rutaImagen, 
                P.idMesa = M.id
            FROM pedidos P
                JOIN mesas R ON M.id = :codigoMesa
            WHERE P.id = :id
        ");
        $consulta->bindValue(':pedido', $pedido->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':rutaImagen', $pedido->rutaImagen);
        $consulta->bindValue(':codigoMesa', $pedido->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':id', $pedido->id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarPedido($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("
            UPDATE pedidos SET fechaBaja = :fechaBaja WHERE id = :id
        ");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }
}