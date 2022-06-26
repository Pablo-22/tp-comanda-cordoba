<?php

class ProductoPedido
{
    public $id;
    public $codigoPedido;
    public $cantidad;
    public $idProducto;
    public $producto;
	public $estado;
	public $tiempoEstimado;

    public function CargarProductoPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            INSERT INTO productos_pedidos (idPedido, idProducto, cantidad) 
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

    public static function obtenerProductosDePedido($codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT PP.id, 
                PS.codigo AS codigoPedido, 
                PP.cantidad, 
                PP.idProducto,
                PR.nombre AS nombreProducto,
				PP.tiempoEstimado,
				E.descripcion AS estado
            FROM productos_pedidos PP
                JOIN productos PR ON PR.id = PP.idProducto
				JOIN pedidos PS ON PS.id = PP.idPedido

				LEFT JOIN ( -- Obtener el último estado
					SELECT EP.idEntidad AS idProductoPedido, EP.descripcion
					FROM estados_productos_pedidos EP
						JOIN (
							SELECT id, 
								idEntidad AS idProductoPedido, 
								MAX(fechaInsercion) AS fechaInsercion
							FROM estados_productos_pedidos
							GROUP BY idEntidad
						) EP2 ON EP2.idProductoPedido = EP.idEntidad 
								AND EP2.fechaInsercion = EP.fechaInsercion
				) E ON E.idProductoPedido = PP.id
            WHERE PS.codigo = :codigoPedido
				AND PP.fechaBaja IS NULL
		");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    }

	public static function obtenerProductoPedido($idProductoPedido) {
		$objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT PP.id, 
                PS.codigo AS codigoPedido, 
                PP.cantidad, 
                PP.idProducto,
                PR.nombre AS nombreProducto,
				PP.tiempoEstimado,
				E.descripcion AS estado
            FROM productos_pedidos PP
                JOIN productos PR ON PR.id = PP.idProducto
				JOIN pedidos PS ON PS.id = PP.idPedido

				LEFT JOIN ( -- Obtener el último estado
					SELECT EP.idEntidad AS idProductoPedido, EP.descripcion
					FROM estados_productos_pedidos EP
						JOIN (
							SELECT id, 
								idEntidad AS idProductoPedido, 
								MAX(fechaInsercion) AS fechaInsercion
							FROM estados_productos_pedidos
							GROUP BY idEntidad
						) EP2 ON EP2.idProductoPedido = EP.idEntidad 
								AND EP2.fechaInsercion = EP.fechaInsercion
				) E ON E.idProductoPedido = PP.id
            WHERE PP.id = :idProductoPedido
				AND PP.fechaBaja IS NULL
        ");
        $consulta->bindValue(':idProductoPedido', $idProductoPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('ProductoPedido');
	}


	public static function ModificarProductoPedido($productoPedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("
            UPDATE productos_pedidos PP
				JOIN pedidos PS ON PS.codigo = :codigoPedido
            SET PP.idPedido = PS.id, 
				PP.cantidad = :cantidad, 
				PP.idProducto = :idProducto,
				PP.tiempoEstimado = :tiempoEstimado
            WHERE PP.id = :id
        ");
        $consulta->bindValue(':codigoPedido', $productoPedido->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idProducto', $productoPedido->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':id', $productoPedido->id, PDO::PARAM_INT);
        $consulta->bindValue(':cantidad', $productoPedido->cantidad, PDO::PARAM_INT);
        $consulta->bindValue(':tiempoEstimado', $productoPedido->tiempoEstimado, PDO::PARAM_INT);
        $consulta->execute();
    }
}