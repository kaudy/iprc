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

		$documentos = array();
		if(!isset($data)) {
			$data['msg'] = "";
			$data['msg_type'] = "";
			$data['errors'] = [];
		}

		if($this->request->getMethod() === 'post') {
			//echo "<pre>";var_dump($this->request->getPost());exit;
			$nome = $this->request->getPost('nome');
			$documentos = $this->documento->listar(array("nome" => $nome));
		}

		// TODO: Criar regra para permissões de cadastro de documentos
		$permite_cadastrar_documento = true;



		//Permissões
		$this->smarty->assign("permite_cadastrar_documento", $permite_cadastrar_documento);
		// Dados
		$this->smarty->assign("documentos", $documentos);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/documento/listar.tpl');
	}


	/**
	 * Upload de arquivos
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