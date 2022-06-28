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

	public static function obtenerProductoPedido($idProductoPedido, $codigoPedido) {
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
				AND PS.codigo = :codigoPedido
				AND PP.fechaBaja IS NULL
        ");
        $consulta->bindValue(':idProductoPedido', $idProductoPedido, PDO::PARAM_STR);
		$consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('ProductoPedido');
	}

	public static function obtenerProductosPendientesDB() {
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
            WHERE PP.fechaBaja IS NULL
				AND E.descripcion = 'Pendiente'
        ");
        $consulta->execute();

        return $consulta->fetchObject('ProductoPedido');
	}

	public static function obtenerProductosPendientesPorRolDB($rol) {
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
            WHERE PP.fechaBaja IS NULL
				AND E.descripcion = 'Pendiente'
				AND (PR.idRolEncargado = (SELECT id FROM roles WHERE descripcion = :rol1) OR :rol2 = 'socio')
        ");
        $consulta->bindValue(':rol1', $rol, PDO::PARAM_STR);
        $consulta->bindValue(':rol2', $rol, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
	}

	public static function obtenerProductosEnPreparacionPorRolDB($rol) {
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
            WHERE PP.fechaBaja IS NULL
				AND E.descripcion = 'En preparación'
				AND (PR.idRolEncargado = (SELECT id FROM roles WHERE descripcion = :rol1) OR :rol2 = 'socio')
        ");
        $consulta->bindValue(':rol1', $rol, PDO::PARAM_STR);
        $consulta->bindValue(':rol2', $rol, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
	}


	public static function obtenerProductosListosPorRolDB($rol) {
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
            WHERE PP.fechaBaja IS NULL
				AND E.descripcion = 'Listo para servir'
				AND (PR.idRolEncargado = (SELECT id FROM roles WHERE descripcion = :rol1) OR :rol2 = 'socio')
        ");

        $consulta->bindValue(':rol1', $rol, PDO::PARAM_STR);
        $consulta->bindValue(':rol2', $rol, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
	}


	public static function modificarProductoPedido($productoPedido)
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

	public function rellenarProductoPedido(){
		$this->producto = Producto::obtenerProducto($this->idProducto);
		if($this->producto->tiempoEstimado) {
			$this->tiempoEstimado = $this->producto->tiempoEstimado;
		}
	}

	public static function obtenerProductosPendientesPorRol($rol) {
		$productosPendientes = ProductoPedido::obtenerProductosPendientesPorRolDB($rol);

		if ($productosPendientes) {
			foreach ($productosPendientes as $productoPedido) {
				if ($productoPedido) {
					$productoPedido->rellenarProductoPedido();
				}
			}
		}

		return $productosPendientes;
	}

	public static function obtenerProductosEnPreparacionPorRol($rol) {
		$productosPendientes = ProductoPedido::obtenerProductosEnPreparacionPorRolDB($rol);

		if ($productosPendientes) {
			foreach ($productosPendientes as $productoPedido) {
				if ($productoPedido) {
					$productoPedido->rellenarProductoPedido();
				}
			}
		}

		return $productosPendientes;
	}

	public static function obtenerProductosListosPorRol($rol) {
		$productosListos = ProductoPedido::obtenerProductosListosPorRolDB($rol);

		if ($productosListos) {
			foreach ($productosListos as $productoPedido) {
				if ($productoPedido) {
					$productoPedido->rellenarProductoPedido();
				}
			}
		}

		return $productosListos;
	}

	public static function borrarProductosDePedidoDB($idPedido) {
		$objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("
            UPDATE productos_pedidos 
			SET fechaBaja = :fechaBaja WHERE idPedido = :idPedido
        ");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
	}
}