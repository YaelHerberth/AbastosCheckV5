<?php
require_once __DIR__ . "/../includes/app.php";

use MVC\Router;
use Controllers\PaginasController;
use Controllers\AuthController;
use Controllers\DepartamentosController;
use Controllers\PedidosController;
use Controllers\ProductosController;
use Controllers\ProfileController;
use Controllers\UsuariosController;

$router = new Router;

session_start();

// Las rutas se registran solas también como /public/... para el hosting (ver Router::variantes).
// Las URLs anteriores (/departamentoss, /productoss, /users...) se redirigen en Router::URLS_ANTERIORES.

// ---------- Tienda (público) ----------
$router->get('/',[PaginasController::class, 'index']);
$router->post('/',[PaginasController::class, 'index']);
$router->get('/departamentos',[PaginasController::class, 'departamentos']);
$router->get('/departamento',[PaginasController::class, 'productos']);
$router->post('/departamento',[PaginasController::class, 'productos']);
$router->get('/buscar',[PaginasController::class, 'buscar']);
$router->post('/buscar',[PaginasController::class, 'buscar']);
$router->get('/conocenos',[PaginasController::class, 'conocenos']);
$router->get('/avisos-privacidad',[PaginasController::class, 'avisos']);
$router->get('/terminos-condiciones',[PaginasController::class, 'terminos']);

// Carrito
$router->get('/carrito',[PaginasController::class, 'carrito']);
$router->post('/carrito',[PaginasController::class, 'carrito']);
$router->post('/carrito/ajax',[PaginasController::class, 'carritoAjax']);
$router->get('/comprar',[PaginasController::class, 'comprar']);
$router->post('/comprar',[PaginasController::class, 'comprar']);

// ---------- Cuenta del cliente (requiere sesión) ----------
$router->get('/profile',[ProfileController::class, 'profile']);
$router->get('/mis-pedidos',[PaginasController::class, 'misPedidos']);
$router->get('/mis-pedidos/ver',[PaginasController::class, 'verPedido']);
$router->post('/mis-pedidos/repetir',[PaginasController::class, 'repetirPedido']);

// ---------- Administración (todo /admin requiere rol admin) ----------
$router->get('/admin', function () { redirigir('/admin/metricas'); });
$router->get('/admin/metricas',[PedidosController::class, 'panel']);

// Pedidos
$router->get('/admin/pedidos',[PedidosController::class, 'index']);
$router->get('/admin/pedidos/ver',[PedidosController::class, 'ver']);
$router->post('/admin/pedidos/estado',[PedidosController::class, 'estado']);

// Usuarios
$router->get('/admin/usuarios',[UsuariosController::class, 'index']);
$router->get('/admin/usuarios/crear',[UsuariosController::class, 'crear']);
$router->post('/admin/usuarios/crear',[UsuariosController::class, 'crear']);
$router->get('/admin/usuarios/actualizar',[UsuariosController::class, 'actualizar']);
$router->post('/admin/usuarios/actualizar',[UsuariosController::class, 'actualizar']);
$router->post('/admin/usuarios/eliminar',[UsuariosController::class, 'eliminar']);

// Departamentos
$router->get('/admin/departamentos',[DepartamentosController::class, 'index']);
$router->get('/admin/departamentos/crear',[DepartamentosController::class, 'crear']);
$router->post('/admin/departamentos/crear',[DepartamentosController::class, 'crear']);
$router->get('/admin/departamentos/actualizar',[DepartamentosController::class, 'actualizar']);
$router->post('/admin/departamentos/actualizar',[DepartamentosController::class, 'actualizar']);
$router->post('/admin/departamentos/eliminar',[DepartamentosController::class, 'eliminar']);

// Productos
$router->get('/admin/productos',[ProductosController::class, 'index']);
$router->get('/admin/productos/crear',[ProductosController::class, 'crear']);
$router->post('/admin/productos/crear',[ProductosController::class, 'crear']);
$router->get('/admin/productos/actualizar',[ProductosController::class, 'actualizar']);
$router->post('/admin/productos/actualizar',[ProductosController::class, 'actualizar']);
$router->post('/admin/productos/eliminar',[ProductosController::class, 'eliminar']);

// ---------- Autenticación ----------
$router->get('/login',[AuthController::class, 'login']);
$router->post('/login',[AuthController::class, 'login']);
$router->get('/signup',[AuthController::class, 'signup']);
$router->post('/signup',[AuthController::class, 'signup']);
$router->get('/logout',[AuthController::class, 'logout']);

$router->comprobarRutas();
