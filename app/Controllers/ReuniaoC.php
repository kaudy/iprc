<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Reuniao;
use App\Models\Usuario;
use App\Models\Regra;
use App\Models\TipoStatus;

class ReuniaoC extends BaseController {

	protected $reuniao;
	protected $usuario;
	protected $regra;
	protected $tipoStatus;

	public function __construct() {
		$this->reuniao = model(Reuniao::class);
		$this->usuario = model(Usuario::class);
		$this->regra = model(Regra::class);
		$this->tipoStatus = model(TipoStatus::class);

	}

	public function index() {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao)) {
			return redirect()->route('login');
		}
		// mensagem temporaria da sessao
		$data = $this->session->getFlashdata('data');

		if(!isset($data)) {
			$data['msg'] = "";
			$data['msg_type'] = "";
			$data['errors'] = [];
		}

		// Carrega lista de usuários
		$reunioes = $this->reuniao->listar();
		// Carrega os tipos de status
		$tipos_status = $this->tipoStatus->whereIn('id', array('1','3', '5'))->findAll();

		$this->smarty->assign("tipos_status", $tipos_status);
		$this->smarty->assign("reunioes", $reunioes);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/reuniao/listar.tpl');
	}
}
