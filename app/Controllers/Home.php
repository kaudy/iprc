<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\Usuario;
use App\Models\Share;

class Home extends BaseController {

	protected $usuario;

	public function __construct() {
		$this->usuario = model(Usuario::class);
		$this->share = model(Share::class);
	}

	public function index() {
		if(is_null($this->session->get('usuario'))) {
			return redirect()->route('login');
		}
		$usuario_sessao = $this->session->get('usuario');
		// Todas globais ativas
		//echo "<pre>";var_dump($GLOBALS);exit;

		// Ultima url acessada
		//echo "<pre>";var_dump($this->session->get('_ci_previous_url'));exit;

		// Base url da aplicação
		//echo "<pre>";var_dump(base_url());exit;

		// Recurepa variavel do env
		//echo "<pre>";var_dump(getenv('CI_ENVIRONMENT'));exit;

		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'default.tpl');
	}
}
