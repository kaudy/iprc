<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Usuario
$routes->group('usuario', static function ($routes) {
	$routes->get('', 'UsuarioC::index');
	$routes->post('', 'UsuarioC::index');
	$routes->match(['get', 'post'], 'cadastrar', 'UsuarioC::cadastrar', ['as' => 'usuario_cadastar']);
	$routes->match(['get', 'post'], '(:num)/alterar', 'UsuarioC::alterar/$1', ['as' => 'usuario_alterar']);
	$routes->match(['get', 'post'], '(:num)/visualizar', 'UsuarioC::visualizar/$1', ['as' => 'usuario_visualizar']);
	$routes->match(['get', 'post'], '(:num)/reenviarAtivacao', 'UsuarioC::reenviaEmailAtivacao/$1', ['as' => 'usuario_reenviar_ativacao']);
	$routes->match(['get', 'post'], 'mail', 'UsuarioC::mail', ['as' => 'usuario_teste_email']);
});
$routes->match(['get', 'post'], 'login', 'UsuarioC::login');
$routes->match(['get', 'post'], 'logout', 'UsuarioC::logout');
$routes->match(['get', 'post'], 'registrar', 'UsuarioC::registrar');
$routes->match(['get', 'post'], 'ativacao/(:num)/(:segment)', 'UsuarioC::ativarUsuario/$1/$2', ['as' => 'usuario_ativar']);
$routes->match(['get', 'post'], 'recuperar_senha', 'UsuarioC::recuperarSenha',  ['as' => 'usuario_recuperar_senha']);
$routes->match(['get', 'post'], 'meus_dados', 'UsuarioC::meus_dados',  ['as' => 'usuario_meus_dados']);

// Votacão
$routes->group('votacao', static function ($routes) {
	$routes->get('', 'VotacaoC::index');
	$routes->post('', 'VotacaoC::index');
	$routes->match(['get', 'post'], 'cadastrar', 'VotacaoC::cadastrarVotacao', ['as' => 'votacao_cadastar']);
	//Grupos
	$routes->match(['get', 'post'], '(:num)/grupos/cadastrar', 'VotacaoC::cadastrarGrupos/$1', ['as' => 'votacao_cadastar_grupos']);
	$routes->match(['get', 'post'], '(:num)/grupos/(:num)/remover', 'VotacaoC::removerGrupo/$1/$2', ['as' => 'votacao_remover_grupo']);
	//Opções
	$routes->match(['get', 'post'], '(:num)/opcoes/cadastrar', 'VotacaoC::cadastrarOpcoes/$1', ['as' => 'votacao_cadastar_opcoes']);
	$routes->match(['get', 'post'], '(:num)/opcoes/(:num)/remover', 'VotacaoC::removerOpcao/$1/$2', ['as' => 'votacao_remover_opcao']);
	//Fiscais
	$routes->match(['get', 'post'], '(:num)/fiscais/cadastrar', 'VotacaoC::cadastrarFiscais/$1', ['as' => 'votacao_cadastar_fiscais']);
	$routes->match(['get', 'post'], '(:num)/fiscais/(:num)/remover', 'VotacaoC::removerFiscal/$1/$2', ['as' => 'votacao_remover_fiscal']);
	//Acoes
	$routes->match(['get', 'post'], '(:num)/alterar', 'VotacaoC::alterarVotacao/$1', ['as' => 'votacao_alterar']);
	$routes->match(['get', 'post'], '(:num)/visualizar', 'VotacaoC::visualizar/$1', ['as' => 'votacao_visualizar']);
	$routes->match(['get', 'post'], '(:num)/cancelar', 'VotacaoC::cancelarVotacao/$1', ['as' => 'votacao_cancelar']);
	$routes->match(['get', 'post'], '(:num)/ativar', 'VotacaoC::ativarVotacao/$1', ['as' => 'votacao_ativar']);
	$routes->match(['get', 'post'], '(:num)/votar', 'VotacaoC::votar/$1', ['as' => 'votacao_votar']);
	$routes->match(['get', 'post'], '(:num)/resultado', 'VotacaoC::resultado/$1', ['as' => 'votacao_resultado']);
	$routes->match(['get', 'post'], '(:num)/finalizar', 'VotacaoC::finalizar/$1', ['as' => 'votacao_finalizar']);
});

// Reunião
$routes->group('reuniao', static function ($routes) {
	$routes->get('', 'ReuniaoC::index');
	$routes->post('', 'ReuniaoC::index');
	//Grupos
	$routes->match(['get', 'post'], '(:num)/grupos/cadastrar', 'ReuniaoC::cadastrarGrupos/$1', ['as' => 'reuniao_cadastar_grupos']);
	$routes->match(['get', 'post'], '(:num)/grupos/(:num)/remover', 'ReuniaoC::removerGrupo/$1/$2', ['as' => 'reuniao_remover_grupo']);
	// Presenças
	$routes->match(['get', 'post'], '(:num)/presenca/gerenciar', 'ReuniaoC::gerenciarPresencasReuniao/$1', ['as' => 'reuniao_presenca_gerenciar']);
	$routes->match(['get', 'post'], '(:num)/presenca/(:num)/justificar/(:segment)', 'ReuniaoC::justificarReuniao/$1/$2/$3', ['as' => 'reuniao_presenca_justificar']);
	$routes->match(['get', 'post'], '(:num)/presenca/(:num)/confirmar/(:segment)', 'ReuniaoC::confirmarPresencaReuniao/$1/$2/$3', ['as' => 'reuniao_presenca_confirmar']);
	//Acoes
	$routes->match(['get', 'post'], 'cadastrar', 'ReuniaoC::cadastrarReuniao', ['as' => 'reuniao_cadastar']);
	$routes->match(['get', 'post'], '(:num)/alterar', 'ReuniaoC::alterarReuniao/$1', ['as' => 'reuniao_alterar']);
	$routes->match(['get', 'post'], '(:num)/visualizar', 'ReuniaoC::visualizar/$1', ['as' => 'reuniao_visualizar']);
	$routes->match(['get', 'post'], '(:num)/ativar', 'ReuniaoC::ativarReuniao/$1', ['as' => 'reuniao_ativar']);
	$routes->match(['get', 'post'], '(:num)/cancelar', 'ReuniaoC::cancelarReuniao/$1', ['as' => 'reuniao_cancelar']);
	$routes->match(['get', 'post'], '(:num)/finalizar', 'ReuniaoC::finalizarReuniao/$1', ['as' => 'reuniao_finalizar']);
	$routes->match(['get', 'post'], '(:num)/justificar', 'ReuniaoC::justificarReuniao/$1', ['as' => 'reuniao_justificar']);
	$routes->match(['get', 'post'], '(:num)/documentos/(:num)/remover', 'ReuniaoC::removerDocumento/$1/$2', ['as' => 'reuniao_remover_documento']);
});

// Documentos
$routes->group('documento', static function ($routes) {
	$routes->get('', 'DocumentoC::index');
	$routes->post('', 'DocumentoC::index');
	$routes->match(['get', 'post'], 'cadastrar', 'DocumentoC::cadastrarDocumento', ['as' => 'documento_cadastar']);
	$routes->match(['get', 'post'], '(:num)/alterar', 'DocumentoC::alterarDocumento/$1', ['as' => 'documento_alterar']);
	$routes->match(['get', 'post'], '(:num)/remover', 'DocumentoC::removerDocumento/$1', ['as' => 'documento_remover']);
});

// Email
$routes->match(['get', 'post'], 'mail', 'UsuarioC::mail');

// Download Documentos
$routes->get('download/(:segment)', 'DocumentoC::download/$1', ['as' => 'documento_download']);

// Cron Temporaria
$routes->match(['get', 'post'], 'agendar_envio_email', 'ReuniaoC::agendarEnvioEmailReuniao');

// Cron
$routes->group('cron', static function ($routes) {
	$routes->match(['get', 'post'], 'enviar_email_agendado', 'CronC::enviarEmails');
});