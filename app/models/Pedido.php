<?php

class Pedido
{
    public $id;
    public $codigo;
    public $rutaImagen;
    public $codigoMesa;
    public $nombreCliente;
	public $productosPedidos;
	public $estado;
	public $tiempoEstimado;

    public function CrearPedidoDB()
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

    public static function ObtenerTodosDB()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT P.id, 
                P.codigo, 
                P.rutaImagen, 
                R.codigo as codigoMesa,
                P.nombreCliente,
                E.descripcion as estado
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
        ");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function ObtenerPedidoDB($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT P.id, 
                P.codigo, 
                P.rutaImagen, 
                M.codigo as codigoMesa,
                P.nombreCliente,
                E.descripcion as estado
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
            WHERE P.codigo = :codigoPedido
        ");
        $consulta->bindValue(':codigoPedido', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

	public static function ObtenerPedidosPendientesDB()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
			SELECT P.id, 
				P.codigo, 
				P.rutaImagen, 
				M.codigo as codigoMesa,
				P.nombreCliente,
                E.descripcion as estado
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
			
			WHERE E.Descripcion = 'Pendiente'
        ");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function ModificarPedidoDB($pedido)
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

    public static function BorrarPedidoDB($id)
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

	public static function ObtenerPedidosPorMesa($codigoMesa)
	{
		$objAccesoDatos = AccesoDatos::obtenerInstancia();
		$consulta = $objAccesoDatos->prepararConsulta("
			SELECT P.id, 
				P.codigo, 
				P.rutaImagen, 
				M.codigo as codigoMesa,
				P.nombreCliente,
				E.descripcion as estado
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
			WHERE M.codigo = :codigoMesa
		");

		$consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
		$consulta->execute();
		return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
	}

	public static function ObtenerPedido($codigoPedido) {
		$pedido = Pedido::ObtenerPedidoDB($codigoPedido);

		$pedido->productosPedidos = ProductoPedido::obtenerProductosDePedido($codigoPedido);
		foreach ($pedido->productosPedidos as $producto) {
			$producto->producto = Producto::obtenerProducto($producto->idProducto);
		}
		$pedido->tiempoEstimado = max(array_map(function($productoPedido) {
			return $productoPedido->tiempoEstimado;
		}, $pedido->productosPedidos));

		return $pedido;
	}

	public static function ObtenerTodos(){
		$lista = Pedido::ObtenerTodosDB();

		foreach ($lista as $pedido) {
			$pedido->productosPedidos = ProductoPedido::obtenerProductosDePedido($pedido->codigo);
			foreach ($pedido->productosPedidos as $producto) {
				$producto->producto = Producto::obtenerProducto($producto->idProducto);
			}

			$pedido->tiempoEstimado = max(array_map(function($productoPedido) {
				return $productoPedido->tiempoEstimado;
			}, $pedido->productosPedidos));
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