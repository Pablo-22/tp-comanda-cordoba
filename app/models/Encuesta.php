<?php

class Encuesta
{
    public $id;
    public $codigoMesa;
    public $mesa;
    public $codigoPedido;
    public $pedido;
    public $puntuacionMesa;
    public $puntuacionMozo;
    public $puntuacionCocinero;
    public $puntuacionRestaurante;
    public $descripcion;

    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            INSERT INTO encuestas (
				idMesa, 
				idPedido, 
				puntuacionMesa, 
				puntuacionMozo,
				puntuacionCocinero,
				puntuacionRestaurante,
				descripcion
			) 
            SELECT 
                (SELECT M.id FROM mesas M WHERE M.codigo = :codigoMesa),
				(SELECT P.id FROM pedidos P WHERE P.codigo = :codigoPedido),
				:puntuacionMesa,
				:puntuacionMozo,
				:puntuacionCocinero,
				:puntuacionRestaurante,
				:descripcion
        ");

        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_STR);
		$consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
		$consulta->bindValue(':puntuacionMesa', $this->puntuacionMesa, PDO::PARAM_INT);
		$consulta->bindValue(':puntuacionMozo', $this->puntuacionMozo, PDO::PARAM_INT);
		$consulta->bindValue(':puntuacionCocinero', $this->puntuacionCocinero, PDO::PARAM_INT);
		$consulta->bindValue(':puntuacionRestaurante', $this->puntuacionRestaurante, PDO::PARAM_INT);
		$consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
		$consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT E.id, 
				E.idMesa, 
				E.idPedido, 
				E.puntuacionMesa, 
				E.puntuacionMozo,
				E.puntuacionCocinero,
				E.puntuacionRestaurante,
				E.descripcion
			FROM encuestas E
			WHERE E.fechaBaja IS NULL
        ");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

	public static function ObtenerMejorPuntaje()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT (E.puntuacionMesa + E.puntuacionMozo + E.puntuacionCocinero + E.puntuacionRestaurante) as puntaje
			FROM encuestas E
			WHERE E.fechaBaja IS NULL
			ORDER BY (E.puntuacionMesa + E.puntuacionMozo + E.puntuacionCocinero + E.puntuacionRestaurante) DESC
			LIMIT 1
        ");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerEncuestaPorCodigoPedidoDB($codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
			SELECT E.id, 
				M.codigo AS codigoMesa,
				P.codigo AS codigoPedido, 
				E.puntuacionMesa, 
				E.puntuacionMozo,
				E.puntuacionCocinero,
				E.puntuacionRestaurante,
				E.descripcion
			FROM encuestas E
				JOIN pedidos P ON P.id = E.idPedido
				JOIN mesas M ON M.id = E.idMesa
			WHERE P.codigo = :codigoPedido
				AND E.fechaBaja IS NULL
		");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Encuesta');
    }

	public static function obtenerEncuestaDB($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
			SELECT E.id, 
				M.codigo AS codigoMesa,
				P.codigo AS codigoPedido, 
				E.puntuacionMesa, 
				E.puntuacionMozo,
				E.puntuacionCocinero,
				E.puntuacionRestaurante,
				E.descripcion
			FROM encuestas E
				JOIN pedidos P ON P.id = E.idPedido
				JOIN mesas M ON M.id = E.idMesa
			WHERE E.id = :id
				AND E.fechaBaja IS NULL
		");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Encuesta');
    }

	public static function obtenerEncuestasPorPuntajeDB($puntaje)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
			SELECT E.id, 
				M.codigo AS codigoMesa,
				P.codigo AS codigoPedido, 
				E.puntuacionMesa, 
				E.puntuacionMozo,
				E.puntuacionCocinero,
				E.puntuacionRestaurante,
				E.descripcion
			FROM encuestas E
				JOIN pedidos P ON P.id = E.idPedido
				JOIN mesas M ON M.id = E.idMesa
			WHERE E.puntuacionMesa + E.puntuacionMozo + E.puntuacionCocinero + E.puntuacionRestaurante = :puntaje
				AND E.fechaBaja IS NULL
		");
        $consulta->bindValue(':puntaje', $puntaje, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public function modificarEncuesta()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("
            UPDATE encuestas  U
            SET idMesa = (SELECT M.id FROM mesas M WHERE M.codigo = :codigoMesa), 
				idPedido = (SELECT P.id FROM pedidos P WHERE P.codigo = :codigoPedido), 
				puntuacionMesa = :puntuacionMesa, 
				puntuacionMozo = :puntuacionMozo,
				puntuacionCocinero = :puntuacionCocinero,
				puntuacionRestaurante = :puntuacionRestaurante,
				descripcion = :descripcion
            WHERE U.id = :id
				AND E.fechaBaja IS NULL
		");

		$consulta->bindvalue(':id', $this->id, PDO::PARAM_INT);
		$consulta->bindvalue(':codigoMesa', $this->codigoMesa, PDO::PARAM_INT);
		$consulta->bindvalue(':codigoPedido', $this->codigoPedido, PDO::PARAM_INT);
		$consulta->bindvalue(':puntuacionMesa', $this->puntuacionMesa, PDO::PARAM_INT);
		$consulta->bindvalue(':puntuacionMozo', $this->puntuacionMozo, PDO::PARAM_INT);
		$consulta->bindvalue(':puntuacionCocinero', $this->puntuacionCocinero, PDO::PARAM_INT);
		$consulta->bindvalue(':puntuacionRestaurante', $this->puntuacionRestaurante, PDO::PARAM_INT);
		$consulta->bindvalue(':descripcion', $this->descripcion, PDO::PARAM_STR);

        $consulta->execute();
    }

    public static function borrarEncuesta($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("
            UPDATE encuestas SET fechaBaja = :fechaBaja WHERE id = :id
        ");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }


	public static function ObtenerEncuesta($id){
		$encuesta = Encuesta::obtenerEncuestaDB($id);
		if ($encuesta) {
			$encuesta->pedido = Pedido::obtenerPedidoDB($encuesta->codigoPedido);
			$encuesta->mesa = Mesa::obtenerMesaDB($encuesta->codigoMesa);
		}

		return $encuesta;
	}

	public function rellenarEncuesta(){
		$this->pedido = Pedido::obtenerPedido($this->codigoPedido);
		$this->mesa = Mesa::obtenerMesa($this->codigoMesa);
	}
	
	public static function ObtenerEncuestas(){
		$encuestas = Encuesta::obtenerEncuestasDB();

		foreach($encuestas as $encuesta){
			$encuesta->rellenarEncuesta();
		}

		return $encuestas;
	}

	public static function ObtenerMejoresComentarios(){
		$puntaje = Encuesta::ObtenerMejorPuntaje()['puntaje'];

		var_dump($puntaje);
		$encuestas = Encuesta::obtenerEncuestasPorPuntajeDB($puntaje);
		foreach ($encuestas as $encuesta) {
			if ($encuesta) {
				$encuesta->rellenarEncuesta();
			}
		}
		return $encuestas;
	}
}