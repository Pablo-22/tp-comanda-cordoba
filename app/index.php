<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/ProductoController.php';
require_once './middlewares/ControlDeAcceso.php';
require_once './models/Estado.php';
require_once './models/Log.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/login', function (RouteCollectorProxy $group) {
    $group->post('[/]', \UsuarioController::class . ':VerificarCredenciales');
});

// USUARIOS
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':traerTodos')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');

    $group->get('/roles', \UsuarioController::class . ':traerRoles');

    $group->get('/{nombre}', \UsuarioController::class . ':traerUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');

    $group->post('/cargarUno', \UsuarioController::class . ':cargarUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');

    $group->post('/modificarUno', \UsuarioController::class . ':modificarUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');

    $group->post('[/]', \UsuarioController::class . ':borrarUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');
})->add(\AutentificadorJWT::class . ':verificarAcceso');


// PRODUCTOS
$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':traerTodos');
    $group->get('/{nombre}', \ProductoController::class . ':traerUno');


    $group->post('/cargarUno', \ProductoController::class . ':cargarUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');

	$group->post('/modificarUno', \ProductoController::class . ':modificarUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');

	$group->post('/importarCSV', \ProductoController::class . ':ImportarCSV')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');

    $group->post('/borrarUno', \ProductoController::class . ':borrarUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');

})->add(\AutentificadorJWT::class . ':verificarAcceso');


// PEDIDOS
$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class . ':traerTodos');
    $group->get('/pendientes', \PedidoController::class . ':traerPendientes');
    $group->get('/pendientesPorRol', \PedidoController::class . ':traerPendientesPorRol');
    $group->get('/enPreparacionPorRol', \PedidoController::class . ':traerEnPreparacionPorRol');
    $group->get('/porEstado/{estado}', \PedidoController::class . ':obtenerTodosPorEstado');
    $group->get('/{codigo}', \PedidoController::class . ':traerUno');

    $group->post('/cargarUno', \PedidoController::class . ':cargarUno');
    $group->post('/cargarFotoPedido', \PedidoController::class . ':cargarFotoPedido');
    $group->post('/tomarPedido', \PedidoController::class . ':tomarPedido');
    $group->post('/terminarPreparacionProducto', \PedidoController::class . ':terminarPreparacionProducto');
    $group->post('/entregarPedido', \PedidoController::class . ':entregarPedido');
    $group->post('/modificarUno', \PedidoController::class . ':modificarUno');
    $group->post('/cobrarPedido', \PedidoController::class . ':cobrarPedido');

    $group->post('[/]', \PedidoController::class . ':borrarUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');
})->add(\AutentificadorJWT::class . ':verificarAcceso');

// ENCUESTAS
$app->group('/encuestas', function (RouteCollectorProxy $group) {
    $group->get('/mejores', \PedidoController::class . ':traerMejoresComentarios');
})->add(\AutentificadorJWT::class . ':verificarAcceso');


// MESAS
$app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \MesaController::class . ':traerTodos');
    $group->get('/masUsada', \MesaController::class . ':traerMesaMasUsada');


    $group->get('/{nombre}', \MesaController::class . ':traerUno');
    $group->post('/cargarUno', \MesaController::class . ':cargarUno');
    $group->post('/modificarUno', \MesaController::class . ':modificarUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');

    $group->post('/cerrarMesa', \MesaController::class . ':cerrarMesa');
	
    $group->post('[/]', \MesaController::class . ':borrarUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');
})->add(\AutentificadorJWT::class . ':verificarAcceso');


$app->group('/cliente', function (RouteCollectorProxy $group) {
    $group->post('/cargarUno', \PedidoController::class . ':cargarEncuesta');
    $group->get('/descargarCSV', \ProductoController::class . ':descargarCSV');
    $group->get('/descargarPDF', \ProductoController::class . ':descargarPDF');
    $group->get('/tiempo/{codigo}', \PedidoController::class . ':verDemoraPedido');
});


$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("C??rdoba Pablo - TP 'La Comanda'");
    return $response;
});

$app->run();
