<?php

class Encuesta
{
    public $id;
    public $idMesa;
    public $idPedido;
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

    public static function obtenerEncuesta($idMesa)
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
			JOIN puntuacionMesaes R ON R.descripcion = :puntuacionMesa
            SET U.idMesa = :encuesta, 
                U.idPedido = :idPedido,
                U.idRol = R.id
            WHERE U.id = :id
        ");
        $consulta->bindValue(':encuesta', $this->idMesa, PDO::PARAM_STR);
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_STR);
        $consulta->bindValue(':puntuacionMesa', $this->puntuacionMesa, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
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
}