<?php

namespace App\Controllers;

use App\Models\Documento;
use CodeIgniter\Files\File;

class DocumentoC extends BaseController {

	protected $documento;

	public function __construct() {
		$this->documento = model(Documento::class);
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
	}


	/**
	 *
	 */
	public function upload() {
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
	}

	/**
	 * Gerencia o download de um arquivo
	 */
	public function download($arquivo_hash) {
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
		$documento = $this->documento->where('hash', $arquivo_hash)->first();

		if(!$documento) {
			return redirect()->route('/');
		}else {
			// TODO: Fazer contador de downloads
			// TODO: Fazer o histórico de downloads
			//$this->documento->where('hash', $arquivo_hash)->set('downloads', $documento->downloads + 1)->update();


			return $this->response->download("./documentos/{$documento->hash}.{$documento->extensao}", null)->setFileName("{$documento->nome}");;
		}
	}
}