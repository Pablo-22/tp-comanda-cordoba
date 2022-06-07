<?php

class PedidoProductos
{
    public $id;
    public $codigoPedido;
    public $cantidad;
    public $IdProducto;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            INSERT INTO pedidos (idPedido, idProducto, cantidad) 
            SELECT 
                P.id AS idPedido,
                :idProducto AS idProducto,
                :cantidad AS cantidad,
            FROM pedidos P
            WHERE P.codigoPedido = :codigoPedido
            LIMIT 1
        ");

        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':IdProducto', $this->rol, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $ $this->cantidad);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT P.id, 
                PE.codigoPedido, 
                A.cantidad, 
                A.IdProducto
            FROM pedido_productos A
                JOIN pedidos PE ON PE.id = A.idPedido
        ");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProductos');
    }

    public static function obtenerPedido($codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT P.id, 
                P.codigoPedido, 
                P.cantidad, 
                M.codigoPedido as IdProducto
            FROM pedidos M
                JOIN mesas R ON M.id = P.idMesa
            WHERE P.codigoPedido = :codigoPedido
        ");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('PedidoProductos');
    }

    public static function modificarPedido($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("
            UPDATE pedidos 
            SET P.codigoPedido = :codigoPedido, 
                P.cantidad = :cantidad, 
                P.idMesa = M.id
            FROM pedidos P
                JOIN mesas R ON M.id = :IdProducto
            WHERE P.id = :id
        ");
        $consulta->bindValue(':pedido', $pedido->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $pedido->cantidad);
        $consulta->bindValue(':IdProducto', $pedido->IdProducto, PDO::PARAM_STR);
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