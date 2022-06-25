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

        $idPedidoHash = password_hash($this->idPedido, PASSWORD_DEFAULT);
        $consulta->bindValue(':idMesaEncuesta', $this->idMesa, PDO::PARAM_STR);
        $consulta->bindValue(':idPedido', $idPedidoHash);
        $consulta->bindValue(':puntuacionMesa', $this->puntuacionMesa, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function ObtenerTodos()
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
        ");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

	public static function ObtenerMejores()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT E.id
			FROM encuestas E
			ORDER BY SUM(E.puntuacionMesa + E.puntuacionMozo + E.puntuacionCocinero + E.puntuacionRestaurante) DESC
			LIMIT 3
        ");
        $consulta->execute();

        return $consulta->fetchAll();
    }

    public static function obtenerEncuestaDB($idMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
			SELECT E.id, 
				M.codigo AS codigoMesa,, 
				P.codigo AS codigoPedido, 
				E.puntuacionMesa, 
				E.puntuacionMozo,
				E.puntuacionCocinero,
				E.puntuacionRestaurante,
				E.descripcion
			FROM encuestas E
				JOIN pedidos P ON P.id = E.idPedido
				JOIN mesas M ON M.id = E.idMesa
			WHERE E.Id = :idEncuesta
        ");
        $consulta->bindValue(':idEncuesta', $idEncuesta, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Encuesta');
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

		$encuesta->pedido = Pedido::obtenerPedidoDB($encuesta->idPedido);
		$encuesta->mesa = Mesa::obtenerMesaDB($encuesta->idMesa);

		return $encuesta;
	}
	
	public static function ObtenerEncuestas(){
		$encuestas = Encuesta::obtenerEncuestasDB();

		foreach($encuestas as $encuesta){
			$encuesta->pedido = Pedido::obtenerPedidoDB($encuesta->idPedido);
			$encuesta->mesa = Mesa::obtenerMesaDB($encuesta->idMesa);
		}

		return $encuestas;
	}

	public static function ObtenerMejoresComentarios(){
		$idEncuestas = Encuesta::ObtenerMejores();
		$encuestas = array();

		foreach($idEncuestas as $id){
			$encuesta = Encuesta::ObtenerEncuesta($id);
			array_push($encuestas, $encuesta);
		}

		return $encuestas;
	}
}