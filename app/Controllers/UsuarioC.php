<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Usuario;
use App\Models\Pessoa;
use App\Models\Perfil;
use App\Models\TipoStatus;

class UsuarioC extends BaseController {

	protected $usuario;
	protected $pessoa;
	protected $perfil;
	protected $tipoStatus;

	public function __construct() {
		$this->usuario = model(Usuario::class);
		$this->pessoa = model(Pessoa::class);
		$this->perfil = model(Perfil::class);
		$this->tipoStatus = model(TipoStatus::class);
	}

	/**
	 * Listagem de usuários
	 */
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
		//echo "<pre>";var_dump($data);exit;

		// Carrega lista de usuários
		$usuarios = $this->usuario->listar();

		$this->smarty->assign("usuarios", $usuarios);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/usuario/listar.tpl');
	}

	/**
	 * Login do usuário
	 */
	public function login() {
		$usuario_sessao = $this->usuario->check();
		$data['msg'] = "";
		$data['msg_type'] = "";
		$data['errors'] = [];

		if($this->request->getMethod() === 'post') {
			$email = $this->request->getPost('email');
			$senha = $this->request->getPost('senha');

			$usuario_sessao = $this->usuario->check($email, $senha);

			if($usuario_sessao->logado == false) {
				$data['msg'] = "Usuário e/ou senha incorretos.";
				$data['msg_type'] = 'danger';
			}else {
				// Seta os dados do usuário na sessão
				$this->session->set('usuario', $usuario_sessao);

				// Redireciona para tela principal
				return redirect()->route('/');
			}
		}

		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/usuario/login.tpl');
	}

	/**
	 * Logout do usuário e remove a sessão ativa
	 */
	public function logout() {
		if(!is_null($this->session->get('usuario'))) {
			$this->session->destroy();
		}
		return redirect()->route('login');
	}

	/**
	 * Registro de usuário
	 */
	public function registrar($passo=1) {
		$usuario_sessao = $this->session->get('usuario');
		if(!is_null($usuario_sessao)) {
			return redirect()->route('/');
		}
		$usuario_sessao = $this->usuario->check();
		$data['msg'] = "";
		$data['msg_type'] = "";
		$data['errors'] = [];

		$data['msg'] = "";
		$data['msg_type'] = "";
		$data['errors'] = [];

		//echo "<pre>";var_dump("Registrar");exit;
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/usuario/registrar.tpl');
	}

	/**
	 * Cadastro de usuário/pessoa internamente
	 */
	public function cadastrar() {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao)) {
			return redirect()->route('login');
		}
		$data['msg'] = "";
		$data['msg_type'] = "";
		$data['errors'] = [];

		if($this->request->getMethod() === 'post') {
			$status = true;

			$nome = ucwords(strtolower($this->request->getPost('nome')));
			$tipo_documento = $this->request->getPost('tipo_documento');
			$documento = $tipo_documento == "EXT" ? $this->request->getPost('documento') : somenteNumeros($this->request->getPost('documento'));
			$data_nascimento = $this->request->getPost('data_nascimento');
			$sexo = $this->request->getPost('sexo');
			$email = strtolower($this->request->getPost('email'));
			$senha = strtolower($this->request->getPost('senha'));
			$telefone = $this->request->getPost('telefone');
			$estado_civil = $this->request->getPost('estado_civil');
			$telefone = $this->request->getPost('telefone');
			$perfil_id = $this->request->getPost('perfil_id');

			// Validação dos dados
			$result = $this->pessoa->where('email', $email)->first();
			if($result && $result->email == $email) {
				$data['msg'] = "Usuário não cadastrado. Erros encontrados:";
				$data['msg_type'] = "danger";
				array_push($data['errors'], "Email já cadastrado.");
				$status = false;
			}
			$result = $this->pessoa->where('documento', $documento)->where('tipo_documento', $tipo_documento)->first();
			if($result && $result->email == $email) {
				$data['msg'] = "Usuário não cadastrado. Erros encontrados:";
				$data['msg_type'] = "danger";
				array_push($data['errors'], "Documento já cadastrado.");
				$status = false;
			}

			if($status) {
				// Cria nova pessoa e cria usuário
				$dados = (object) array(
					"documento" => $documento,
					"tipo_documento" => $tipo_documento,
					"nome" => $nome,
					"sexo" => $sexo,
					"data_nascimento" => $data_nascimento,
					"estado_civil" => $estado_civil,
					"telefone" => $telefone,
					"email" => $email,
					"perfil_id" => $perfil_id,
					"senha" => $senha != null && $senha != '' ? $senha : "{$documento}{$email}",
					"chave_ativacao" => "{$documento}{$email}",
					"data_cadastro" => date('Y-m-d H:i:s'),
					"usuario_cadastro_id" => $usuario_sessao->usuario->id
				);
				$novo_usuario = $this->usuario->adicionar($dados);
				if($novo_usuario) {
					$data['msg'] = "Usuário cadastrado com sucesso!";
					$data['msg_type'] = "primary";
					return redirect()->route('usuario')->with('data', $data);
				}else {
					$data['msg'] = "Usuário não cadastrado. Erros encontrados:";
					$data['msg_type'] = "danger";
					array_push($data['errors'], $novo_usuario);
					$status = false;
				}
			}
		}

		// Carrega todos os perfis ativos
		$perfis = $this->perfil->where('status', 1)->findAll();

		$this->smarty->assign("perfis", $perfis);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("estados_civil", getEnum('pessoas', 'estado_civil'));
		$this->smarty->assign("tipos_sexos", getEnum('pessoas', 'sexo'));
		$this->smarty->assign("tipos_documento", getEnum('pessoas', 'tipo_documento'));
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/usuario/cadastrar.tpl');
	}

	/**
	 * Altera os dados do usuário
	 */
	public function alterar($usuario_id) {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao)) {
			return redirect()->route('login');
		}
		$data['msg'] = "";
		$data['msg_type'] = "";
		$data['errors'] = [];

		// Usuário
		$usuario = $this->usuario->where('id', $usuario_id)->first();
		// Pessoa
		$pessoa = $this->pessoa->where('id', $usuario->pessoa_id)->first();

		if($this->request->getMethod() === 'post') {
			$status = true;

			$nome = ucwords(strtolower($this->request->getPost('nome')));
			$tipo_documento = $this->request->getPost('tipo_documento');
			$documento = $tipo_documento == "EXT" ? $this->request->getPost('documento') : somenteNumeros($this->request->getPost('documento'));
			$data_nascimento = $this->request->getPost('data_nascimento');
			$sexo = $this->request->getPost('sexo');
			$email = strtolower($this->request->getPost('email'));
			$senha = trim($this->request->getPost('senha')) != '' ? strtolower($this->request->getPost('senha')) : null;
			$estado_civil = $this->request->getPost('estado_civil');
			$telefone = $this->request->getPost('telefone');
			$perfil_id = $this->request->getPost('perfil_id');
			$tipo_status_id = $this->request->getPost('tipo_status_id') != null && $this->request->getPost('tipo_status_id') != '' ? $this->request->getPost('tipo_status_id') : null;

			// Validação dos dados
			$result = $this->pessoa->where('email', $email)->where('id !=', $usuario->pessoa_id)->first();
			if($result && $result->email == $email) {
				$data['msg'] = "Usuário não cadastrado. Erros encontrados:";
				$data['msg_type'] = "danger";
				array_push($data['errors'], "Email já cadastrado.");
				$status = false;
			}
			$result = $this->pessoa->where('documento', $documento)->where('tipo_documento', $tipo_documento)->where('id !=', $usuario->pessoa_id)->first();
			if($result && $result->email == $email) {
				$data['msg'] = "Usuário não cadastrado. Erros encontrados:";
				$data['msg_type'] = "danger";
				array_push($data['errors'], "Documento já cadastrado.");
				$status = false;
			}

			if($status) {
				// Cria nova pessoa e cria usuário
				$dados = array(
					"usuario_id" => $usuario->id,
					"pessoa_id" => $usuario->pessoa_id,
					"documento" => $documento,
					"tipo_documento" => $tipo_documento,
					"nome" => $nome,
					"sexo" => $sexo,
					"data_nascimento" => $data_nascimento,
					"estado_civil" => $estado_civil,
					"telefone" => $telefone,
					"email" => $email,
					"perfil_id" => $perfil_id,
					"chave_ativacao" => "{$documento}{$email}",
					"data_alteracao" => date('Y-m-d H:i:s'),
					"usuario_alteracao_id" => $usuario_sessao->usuario->id
				);
				if($senha != null) {
					$dados['senha'] = $senha;
				}
				if($tipo_status_id != null && $tipo_status_id != 3) {
					$dados['status'] = $tipo_status_id;
				}

				$usuario_alterado = $this->usuario->alterar((object) $dados);
				if($usuario_alterado) {
					$data['msg'] = "Usuário alterado com sucesso!";
					$data['msg_type'] = "primary";
					return redirect()->route('usuario')->with('data', $data);
				}else {
					$data['msg'] = "Usuário não alterado. Erros encontrados:";
					$data['msg_type'] = "danger";
					array_push($data['errors'], $novo_usuario);
					$status = false;
				}
			}
		}

		// Carrega todos os perfis ativos
		$perfis = $this->perfil->where('status', 1)->findAll();
		// Carrega os tipos de status
		$tipos_status = $this->tipoStatus->whereIn('id', array('1','2'))->findAll();

		//echo "<pre>";var_dump($usuario);exit;

		$this->smarty->assign("usuario", $usuario);
		$this->smarty->assign("pessoa", $pessoa);
		$this->smarty->assign("perfis", $perfis);
		$this->smarty->assign("tipos_status", $tipos_status);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("estados_civil", getEnum('pessoas', 'estado_civil'));
		$this->smarty->assign("tipos_sexos", getEnum('pessoas', 'sexo'));
		$this->smarty->assign("tipos_documento", getEnum('pessoas', 'tipo_documento'));
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/usuario/alterar.tpl');
	}

	public function visualizar() {

	}
}
