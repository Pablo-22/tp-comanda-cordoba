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

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{nombre}', \UsuarioController::class . ':TraerUno');

    $group->post('/CargarUno', \UsuarioController::class . ':CargarUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');

    $group->post('/ModificarUno', \UsuarioController::class . ':ModificarUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');

    $group->delete('[/]', \UsuarioController::class . ':BorrarUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');
})->add(\AutentificadorJWT::class . ':VerificarAcceso');


$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    $group->get('/{nombre}', \ProductoController::class . ':TraerUno');

    $group->post('/CargarUno', \ProductoController::class . ':CargarUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');

	$group->post('/ModificarUno', \ProductoController::class . ':ModificarUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');

	$group->post('/ImportarCSV', \ProductoController::class . ':ImportarCSV')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');

    $group->delete('[/]', \ProductoController::class . ':BorrarUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');

})->add(\AutentificadorJWT::class . ':VerificarAcceso');


$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class . ':TraerTodos');
    $group->get('/Pendientes', \PedidoController::class . ':TraerPendientes');
    $group->get('/{codigo}', \PedidoController::class . ':TraerUno');
    $group->post('/CargarUno', \PedidoController::class . ':CargarUno');
    $group->post('/TomarPedido', \PedidoController::class . ':TomarPedido');
    $group->post('/TerminarPreparacionProducto', \PedidoController::class . ':TerminarPreparacionProducto');
    $group->post('/EntregarPedido', \PedidoController::class . ':EntregarPedido');
    $group->post('/ModificarUno', \PedidoController::class . ':ModificarUno');
    $group->post('/CobrarPedido', \PedidoController::class . ':CobrarPedido');

    $group->delete('[/]', \PedidoController::class . ':BorrarUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');
})->add(\AutentificadorJWT::class . ':VerificarAcceso');


$app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \MesaController::class . ':TraerTodos');
    $group->get('/{nombre}', \MesaController::class . ':TraerUno');
    $group->post('/CargarUno', \MesaController::class . ':CargarUno');
    $group->post('/ModificarUno', \MesaController::class . ':ModificarUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');

    $group->post('/CerrarMesa', \MesaController::class . ':CerrarMesa');
    $group->delete('[/]', \MesaController::class . ':BorrarUno')
		->add(\ControlDeAcceso::class . ':VerificarPermisoSocio');
})->add(\AutentificadorJWT::class . ':VerificarAcceso');


$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("Córdoba Pablo - TP 'La Comanda'");
    return $response;
});

$app->run();
