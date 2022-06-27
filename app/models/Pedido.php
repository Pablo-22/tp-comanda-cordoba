<?php

class Pedido
{
    public $id;
    public $codigo;
    public $rutaImagen;
    public $codigoMesa;
    public $nombreCliente;
	public $productosPedidos;
	public $precioTotal;
	public $estado;
	public $tiempoEstimado;
	public $tiempoFinal;

    public function crearPedidoDB()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            INSERT INTO pedidos (codigo, rutaImagen, idMesa, nombreCliente) 
            SELECT 
                :codigoPedido AS codigo,
                :rutaImagen AS rutaImagen,
                M.id AS idMesa,
                :nombreCliente AS nombreCliente
            FROM mesas M
            WHERE M.codigo = :codigoMesa
		");

        $consulta->bindValue(':codigoPedido', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':rutaImagen', $this->rutaImagen, PDO::PARAM_STR);
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodosDB()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT P.id, 
                P.codigo, 
                P.rutaImagen, 
                R.codigo as codigoMesa,
                P.nombreCliente,
                E.descripcion as estado,

				TIMESTAMPDIFF(MINUTE, 
   					(EP.fechaInsercion),
    				(EP2.fechaInsercion)
				) AS tiempoFinal

            FROM pedidos P
                JOIN mesas R ON R.id = P.idMesa

				JOIN ( -- Obtener el último estado de cada pedido
					SELECT EP.idEntidad AS IdPedido, EP.descripcion
					FROM estados_pedidos EP
						JOIN (
							SELECT id, 
								idEntidad AS IdPedido, 
								MAX(fechaInsercion) AS fechaInsercion
							FROM estados_pedidos
							GROUP BY idEntidad
						) EP2 ON EP2.IdPedido = EP.idEntidad 
								AND EP2.fechaInsercion = EP.fechaInsercion
				) E ON E.IdPedido = P.id

				LEFT JOIN estados_pedidos EP ON EP.idEntidad = P.id AND EP.descripcion = 'Pendiente'
				LEFT JOIN estados_pedidos EP2 ON EP2.idEntidad = P.id AND EP2.descripcion = 'Pedido Entregado'

			WHERE P.fechaBaja IS NULL
        ");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedidoDB($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT P.id, 
                P.codigo, 
                P.rutaImagen, 
                M.codigo as codigoMesa,
                P.nombreCliente,
                E.descripcion as estado,

				TIMESTAMPDIFF(MINUTE, 
   					(EP.fechaInsercion),
    				(EP2.fechaInsercion)
				) AS tiempoFinal
			FROM pedidos P
                JOIN mesas M ON M.id = P.idMesa

				JOIN ( -- Obtener el último estado de cada pedido
					SELECT EP.idEntidad AS IdPedido, EP.descripcion
					FROM estados_pedidos EP
						JOIN (
							SELECT id, 
								idEntidad AS IdPedido, 
								MAX(fechaInsercion) AS fechaInsercion
							FROM estados_pedidos
							GROUP BY idEntidad
						) EP2 ON EP2.IdPedido = EP.idEntidad 
								AND EP2.fechaInsercion = EP.fechaInsercion
				) E ON E.IdPedido = P.id

				LEFT JOIN estados_pedidos EP ON EP.idEntidad = P.id AND EP.descripcion = 'Pendiente'
				LEFT JOIN estados_pedidos EP2 ON EP2.idEntidad = P.id AND EP2.descripcion = 'Pedido Entregado'

            WHERE P.codigo = :codigoPedido
				AND P.fechaBaja IS NULL
		");
        $consulta->bindValue(':codigoPedido', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

	public static function obtenerPedidoFullDB($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT P.id, 
                P.codigo, 
                P.rutaImagen, 
                M.codigo as codigoMesa,
                P.nombreCliente,
                E.descripcion as estado,

				TIMESTAMPDIFF(MINUTE, 
   					(EP.fechaInsercion),
    				(EP2.fechaInsercion)
				) AS tiempoFinal
			FROM pedidos P
                JOIN mesas M ON M.id = P.idMesa

				JOIN ( -- Obtener el último estado de cada pedido
					SELECT EP.idEntidad AS IdPedido, EP.descripcion
					FROM estados_pedidos EP
						JOIN (
							SELECT id, 
								idEntidad AS IdPedido, 
								MAX(fechaInsercion) AS fechaInsercion
							FROM estados_pedidos
							GROUP BY idEntidad
						) EP2 ON EP2.IdPedido = EP.idEntidad 
								AND EP2.fechaInsercion = EP.fechaInsercion
				) E ON E.IdPedido = P.id

				LEFT JOIN estados_pedidos EP ON EP.idEntidad = P.id AND EP.descripcion = 'Pendiente'
				LEFT JOIN estados_pedidos EP2 ON EP2.idEntidad = P.id AND EP2.descripcion = 'Pedido Entregado'

            WHERE P.codigo = :codigoPedido
		");
        $consulta->bindValue(':codigoPedido', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

	public static function obtenerPedidosPendientesDB()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
			SELECT P.id, 
				P.codigo, 
				P.rutaImagen, 
				M.codigo as codigoMesa,
				P.nombreCliente,
                E.descripcion as estado,

				TIMESTAMPDIFF(MINUTE, 
   					(EP.fechaInsercion),
    				(EP2.fechaInsercion)
				) AS tiempoFinal
			FROM pedidos P
				JOIN mesas M ON M.id = P.idMesa
			
				LEFT JOIN ( -- Obtener el último estado de cada pedido
					SELECT EP.idEntidad AS IdPedido, EP.descripcion
					FROM estados_pedidos EP
						JOIN (
							SELECT id, 
								idEntidad AS IdPedido, 
								MAX(fechaInsercion) AS fechaInsercion
							FROM estados_pedidos
							GROUP BY idEntidad
						) EP2 ON EP2.IdPedido = EP.idEntidad 
								AND EP2.fechaInsercion = EP.fechaInsercion
				) E ON E.IdPedido = P.id

				LEFT JOIN estados_pedidos EP ON EP.idEntidad = P.id AND EP.descripcion = 'Pendiente'
				LEFT JOIN estados_pedidos EP2 ON EP2.idEntidad = P.id AND EP2.descripcion = 'Pedido Entregado'
			
			WHERE E.Descripcion = 'Pendiente'
				AND P.fechaBaja IS NULL
		");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function modificarPedidoDB($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("
            UPDATE pedidos P
				JOIN mesas M ON M.id = :codigoMesa
            SET P.codigo = :codigo, 
                P.rutaImagen = :rutaImagen, 
                P.idMesa = M.id,
                P.nombreCliente = :nombreCliente
            WHERE P.id = :id
        ");
        $consulta->bindValue(':codigo', $pedido->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':rutaImagen', $pedido->rutaImagen);
        $consulta->bindValue(':codigoMesa', $pedido->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $pedido->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':id', $pedido->id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarPedidoDB($id)
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

	public static function obtenerPedidosPorMesa($codigoMesa)
	{
		$objAccesoDatos = AccesoDatos::obtenerInstancia();
		$consulta = $objAccesoDatos->prepararConsulta("
			SELECT P.id, 
				P.codigo, 
				P.rutaImagen, 
				M.codigo as codigoMesa,
				P.nombreCliente,
				E.descripcion as estado,

				TIMESTAMPDIFF(MINUTE, 
   					(EP.fechaInsercion),
					(EP2.fechaInsercion)
				) AS tiempoFinal
			FROM pedidos P
				JOIN mesas M ON M.id = P.idMesa
				LEFT JOIN ( -- Obtener el último estado de cada pedido
					SELECT EP.idEntidad AS IdPedido, EP.descripcion
					FROM estados_pedidos EP
						JOIN (
							SELECT id, 
								idEntidad AS IdPedido, 
								MAX(fechaInsercion) AS fechaInsercion
							FROM estados_pedidos
							GROUP BY idEntidad
						) EP2 ON EP2.IdPedido = EP.idEntidad 
								AND EP2.fechaInsercion = EP.fechaInsercion
				) E ON E.IdPedido = P.id

				LEFT JOIN estados_pedidos EP ON EP.idEntidad = P.id AND EP.descripcion = 'Pendiente'
				LEFT JOIN estados_pedidos EP2 ON EP2.idEntidad = P.id AND EP2.descripcion = 'Pedido Entregado'
			WHERE M.codigo = :codigoMesa
				AND P.fechaBaja IS NULL
		");

		$consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
		$consulta->execute();
		return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
	}

	public function rellenarPedido(){
		$this->productosPedidos = ProductoPedido::obtenerProductosDePedido($this->codigo);
		foreach ($this->productosPedidos as $productoPedido) {
			$productoPedido->producto = Producto::obtenerProducto($productoPedido->idProducto);
			if($productoPedido->producto->tiempoEstimado && $productoPedido->tiempoEstimado == null) {
				$productoPedido->tiempoEstimado = $productoPedido->producto->tiempoEstimado;
			}
		}

		$this->tiempoEstimado = max(array_map(function($productoPedido) {
			if ($productoPedido) {
				if($productoPedido->tiempoEstimado) {
					return $productoPedido->tiempoEstimado;
				}
				return null;
			}
		}, $this->productosPedidos));

		$this->precioTotal = array_sum(array_map(function($productoPedido) {
			if ($productoPedido && $productoPedido->producto) {
				return $productoPedido->cantidad * $productoPedido->producto->precio;
			} else {
				return null;
			}
		}, $this->productosPedidos));
	}

	public static function obtenerPedido($codigoPedido) {
		$pedido = Pedido::obtenerPedidoDB($codigoPedido);
		if ($pedido) {
			$pedido->rellenarPedido();
		}

		return $pedido;
	}

	public static function obtenerPedidoFull($codigoPedido) {
		$pedido = Pedido::obtenerPedidoFullDB($codigoPedido);
		if ($pedido) {
			$pedido->rellenarPedido();
		}

		return $pedido;
	}

	public static function obtenerTodos(){
		$lista = Pedido::obtenerTodosDB();

		foreach ($lista as $pedido) {
			if ($pedido) {
				$pedido->rellenarPedido();
			}
		}

		return $lista;
	}

	public static function obtenerPedidosPorEstado($estado){
		$lista = Pedido::obtenerTodosDB();

		$lista = array_filter($lista, function($pedido) use ($estado) {
			return $pedido->estado == $estado;
		});

		foreach ($lista as $pedido) {
			if ($pedido) {
				$pedido->rellenarPedido();
			}
		}

		return $lista;
	}


	public function estaListo() {
		$output = TRUE;
		foreach ($this->productosPedidos as $productoPedido) {
			if ($productoPedido->estado != STATUS_PRODUCTO_LISTO) {
				$output = FALSE;
				break;
			}
		}
		return $output;
	}
}