<?php

class PedidoProductos
{
    public $id;
    public $codigoPedido;
    public $cantidad;
    public $idProducto;

    public function CargarPedido_Productos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            INSERT INTO pedido_productos (idPedido, idProducto, cantidad) 
            SELECT 
                (SELECT id FROM pedidos P WHERE codigo = :codigoPedido LIMIT 1) AS idPedido,
                :idProducto AS idProducto,
                :cantidad AS cantidad
        ");

        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $this->cantidad);
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
                A.idProducto
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
                M.codigoPedido as idProducto
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
                JOIN mesas R ON M.id = :idProducto
            WHERE P.id = :id
        ");
        $consulta->bindValue(':pedido', $pedido->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $pedido->cantidad);
        $consulta->bindValue(':idProducto', $pedido->idProducto, PDO::PARAM_STR);
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