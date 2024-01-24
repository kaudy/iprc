<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Usuario
$routes->group('usuario', static function ($routes) {
	$routes->get('', 'UsuarioC::index');
	$routes->match(['get', 'post'], 'cadastrar', 'UsuarioC::cadastrar', ['as' => 'usuario_cadastar']);
	$routes->match(['get', 'post'], 'alterar/(:num)', 'UsuarioC::alterar/$1', ['as' => 'usuario_alterar']);
	$routes->match(['get', 'post'], 'visualizar/(:num)', 'UsuarioC::visualizar/$1', ['as' => 'usuario_visualizar']);
	$routes->match(['get', 'post'], 'reenviarAtivacao/(:num)', 'UsuarioC::reenviaEmailAtivacao/$1', ['as' => 'usuario_reenviar_ativacao']);
});
$routes->match(['get', 'post'], 'login', 'UsuarioC::login');
$routes->match(['get', 'post'], 'logout', 'UsuarioC::logout');
$routes->match(['get', 'post'], 'registrar', 'UsuarioC::registrar');
$routes->match(['get', 'post'], 'ativacao/(:num)/(:segment)', 'UsuarioC::ativarUsuario/$1/$2', ['as' => 'usuario_ativar']);
$routes->match(['get', 'post'], 'recuperar_senha', 'UsuarioC::recuperarSenha',  ['as' => 'usuario_recuperar_senha']);


$routes->match(['get', 'post'], 'mail', 'UsuarioC::mail');