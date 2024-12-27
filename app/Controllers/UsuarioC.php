<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Usuario;
use App\Models\Pessoa;
use App\Models\Perfil;
use App\Models\TipoStatus;
use App\Models\Grupo;
use App\Models\UsuarioGrupo;
use PHPMailer\PHPMailer\PHPMailer;

class UsuarioC extends BaseController {

	protected $usuario;
	protected $pessoa;
	protected $perfil;
	protected $tipoStatus;
	protected $grupo;
	protected $usuarioGrupo;

	public function __construct() {
		$this->usuario = model(Usuario::class);
		$this->pessoa = model(Pessoa::class);
		$this->perfil = model(Perfil::class);
		$this->tipoStatus = model(TipoStatus::class);
		$this->grupo = model(Grupo::class);
		$this->usuarioGrupo = model(UsuarioGrupo::class);
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
		// mensagem temporaria da sessao
		$data = $this->session->getFlashdata('data');
		if(!isset($data)) {
			$data['msg'] = "";
			$data['msg_type'] = "";
			$data['errors'] = [];
		}

		if($this->request->getMethod() === 'post') {
			$email = $this->request->getPost('email');
			$senha = $this->request->getPost('senha');

			$usuario_sessao = $this->usuario->check($email, $senha);
			if($usuario_sessao == false || $usuario_sessao->logado == false) {
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
			// Grupos usuario
			$usuario_grupos = $this->request->getPost('usuario_grupos') != '' ? $this->request->getPost('usuario_grupos') : null;
			if($usuario_grupos != null) {
				$usuario_grupos = explode(",", $usuario_grupos);
			}

			// Validação dos dados
			$result = $this->pessoa->where('email', $email)->first();
			if($result && $result->email == $email) {
				$data['msg'] = "Usuário não foi cadastrado. Erros encontrados:";
				$data['msg_type'] = "danger";
				array_push($data['errors'], "Email já cadastrado.");
				$status = false;
			}
			$result = $this->pessoa->where('documento', $documento)->where('tipo_documento', $tipo_documento)->first();
			if($result && $result->email == $email) {
				$data['msg'] = "Usuário não foi cadastrado. Erros encontrados:";
				$data['msg_type'] = "danger";
				array_push($data['errors'], "Documento já cadastrado.");
				$status = false;
			}
			//
			// TODO: Adicionar validação do documento pelo tipo do documento
			//

			if($status) {
				// Cria nova pessoa e cria o novo usuário
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
					"usuario_grupos" => $usuario_grupos,
					"chave_ativacao" => "{$documento}{$email}",
					"data_cadastro" => date('Y-m-d H:i:s'),
					"usuario_cadastro_id" => $usuario_sessao->usuario->id
				);
				$novo_usuario = $this->usuario->adicionar($dados);
				if($novo_usuario) {
					// Envia email para ativação do novo usuário
					$usuario = $this->usuario->where('id', $novo_usuario)->first();
					$pessoa = $this->pessoa->where('id', $usuario->pessoa_id)->first();
					$link = url_to('usuario_ativar', $usuario->id, $usuario->chave_ativacao);

					// Monta os dados do template
					$dados_template = (object) array (
						"nome" => primeiroNome($pessoa->nome),
						"link_ativacao" => $link
					);
					$this->smarty->assign("dados_template", $dados_template);
					$template = $this->smarty->fetch($this->smarty->getTemplateDir(0) .'/emails/ativar_usuario.tpl');

					// Monta os dados do email
					$dados_email = (object) array(
						'email_destinatario'=> $pessoa->email,
						'nome_destinatario' => $pessoa->nome,
						'titulo'			=> 'Ativação de usuário',
						'template'			=> $template
					);
					enviaEmail($dados_email);

					// Retorna mensagem de sucesso para tela de login
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
		$perfis = $this->perfil->where('status_id', 1)->findAll();
		// Carrega todos os grupos ativos
		$grupos = $this->grupo->where('status_id', 1)->findAll();

		$this->smarty->assign("perfis", $perfis);
		$this->smarty->assign("grupos", $grupos);
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
			// Grupos usuario
			$usuario_grupos = $this->request->getPost('usuario_grupos') != '' ? $this->request->getPost('usuario_grupos') : null;
			if($usuario_grupos != null) {
				$usuario_grupos = explode(",", $usuario_grupos);
			}

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
					"usuario_grupos" => $usuario_grupos,
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
		$perfis = $this->perfil->where('status_id', 1)->findAll();
		// Carrega os tipos de status
		$tipos_status = $this->tipoStatus->whereIn('id', array('1','2'))->findAll();
		// Usuario Grupos
		$usuario_grupos = $this->usuarioGrupo->listar($usuario->id);
		// Carrega todos os grupos ativos
		$grupos = $this->grupo->where('status_id', 1)->findAll();

		//echo "<pre>";var_dump($usuario);exit;

		$this->smarty->assign("usuario", $usuario);
		$this->smarty->assign("pessoa", $pessoa);
		$this->smarty->assign("perfis", $perfis);
		$this->smarty->assign("tipos_status", $tipos_status);
		$this->smarty->assign("grupos", $grupos);
		$this->smarty->assign("usuario_grupos", $usuario_grupos);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("estados_civil", getEnum('pessoas', 'estado_civil'));
		$this->smarty->assign("tipos_sexos", getEnum('pessoas', 'sexo'));
		$this->smarty->assign("tipos_documento", getEnum('pessoas', 'tipo_documento'));
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/usuario/alterar.tpl');
	}

	/**
	 * Visualizar todos os dados do usuário e pessoa vinculada ao mesmo
	 */
	public function visualizar($usuario_id) {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao)) {
			return redirect()->route('login');
		}
		$data['msg'] = "";
		$data['msg_type'] = "";
		$data['errors'] = [];

		// Usuário
		$usuario = $this->usuario->where('id', $usuario_id)->first();
		if(!isset($usuario_id) || $usuario_id == '' || $usuario_id=null || $usuario == null) {
			$data['msg'] = "Usuário não encontrado!";
			$data['msg_type'] = "danger";
			return redirect()->route('usuario')->with('data', $data);
		}

		// Pessoa
		$pessoa = $this->pessoa->where('id', $usuario->pessoa_id)->first();
		// Carrega perfil do usuario
		$perfil_usuario = $this->perfil->where('id', $usuario->perfil_id)->first();
		// Usuario Grupos
		$usuario_grupos = $this->usuarioGrupo->listar($usuario->id);

		if($this->request->getMethod() === 'post') {
		}

		$this->smarty->assign("usuario", $usuario);
		$this->smarty->assign("pessoa", $pessoa);
		$this->smarty->assign("perfil_usuario", $perfil_usuario);
		$this->smarty->assign("usuario_grupos", $usuario_grupos);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/usuario/visualizar.tpl');
	}

	/**
	 * Efetua ativação do usuário e valida chave de ativação
	 */
	public function ativarUsuario($usuario_id, $chave_ativacao) {
		$usuario_sessao = $this->usuario->check();
		$data['msg'] = "";
		$data['msg_type'] = "";
		$data['errors'] = [];

		// Usuário
		$usuario = $this->usuario->where('id', $usuario_id)->first();
		// Pessoa
		$pessoa = $this->pessoa->where('id', $usuario->pessoa_id)->first();

		// Se usuario, pessoa não existir ou chave de ativação não correta redireciona para tela de login
		if(!$usuario || !$pessoa) {
			return redirect()->route('login');
		}
		if($usuario->chave_ativacao != $chave_ativacao) {
			return redirect()->route('login');
		}

		if($this->request->getMethod() === 'post') {
			$senha = $this->request->getPost('senha');
			$confirmar_senha = $this->request->getPost('confirmar_senha');

			if($senha != $confirmar_senha ||  strlen($senha) < 8 ||  strlen($confirmar_senha) < 8) {
				$data['msg'] = "Senha Inválida";
				$data['msg_type'] = "danger";
				$data['errors'] = [
					'Senha e confirmação devem ser iguais;',
					'Deve conter no minimo 8 digitos;'
				];
			}else {
				$dados = array(
					"usuario_id" => $usuario->id,
					"chave_ativacao" => null,
					"data_alteracao" => date('Y-m-d H:i:s'),
					"usuario_alteracao_id" => $usuario->id,
					"senha" => $senha,
					"status_id" => 1
				);

				$usuario_alterado = $this->usuario->ativarUsuario((object) $dados);
				if($usuario_alterado) {
					$data['msg'] = "Usuário ativado com sucesso!";
					$data['msg_type'] = "primary";
					return redirect()->route('login')->with('data', $data);
				}else {
					$data['msg'] = "Usuário não alterado. Erros encontrados:";
					$data['msg_type'] = "danger";
					array_push($data['errors'], $novo_usuario);
					$status = false;
				}
			}
		}

		$this->smarty->assign("usuario", $usuario);
		$this->smarty->assign("pessoa", $pessoa);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/usuario/ativar.tpl');
	}

	/**
	 * Efetua ativação do usuário e valida chave de ativação
	 */
	public function recuperarSenha() {
		$usuario_sessao = $this->usuario->check();
		$data['msg'] = "";
		$data['msg_type'] = "";
		$data['errors'] = [];

		if($this->request->getMethod() === 'post') {
			$email_recuperacao = $this->request->getPost('email_recuperacao');
			$pessoa = $this->usuario->recuperarSenha($email_recuperacao);
			if($pessoa) {
				// Envia email ao solicitante
				$usuario = $this->usuario->where('pessoa_id', $pessoa->id)->first();
				$link = url_to('usuario_ativar', $usuario->id, $usuario->chave_ativacao);

				// Monta os dados do template
				$dados_template = (object) array (
					"nome" => $pessoa->nome,
					"link_ativacao" => $link
				);
				$this->smarty->assign("dados_template", $dados_template);
				$template = $this->smarty->fetch($this->smarty->getTemplateDir(0) .'/emails/recuperar_senha.tpl');

				// Monta os dados do email
				$dados_email = (object) array(
					'email_destinatario'=> $pessoa->email,
					'nome_destinatario' => $pessoa->nome,
					'titulo'			=> 'Alteração de Senha',
					'template'			=> $template
				);
				enviaEmail($dados_email);
			}

			$data['msg'] = "Solicitado recuperação de senha.<br>Verifique sua caixa de email!";
			$data['msg_type'] = "primary";
			return redirect()->route('login')->with('data', $data);
		}

		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/usuario/recuperar_senha.tpl');
	}

	/**
	 * Reenvia email de ativação de usuário
	 */
	public function reenviaEmailAtivacao($usuario_id) {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao)) {
			return redirect()->route('login');
		}
		$data['msg'] = "";
		$data['msg_type'] = "";
		$data['errors'] = [];

		$usuario = $this->usuario->where('id', $usuario_id)->first();
		$pessoa = $this->pessoa->where('id', $usuario->pessoa_id)->first();
		$link = url_to('usuario_ativar', $usuario->id, $usuario->chave_ativacao);

		// Monta os dados do template
		$dados_template = (object) array (
			"nome" => primeiroNome($pessoa->nome),
			"link_ativacao" => $link
		);
		$this->smarty->assign("dados_template", $dados_template);
		$template = $this->smarty->fetch($this->smarty->getTemplateDir(0) .'/emails/ativar_usuario.tpl');

		// Monta os dados do email
		$dados_email = (object) array(
			'email_destinatario'=> $pessoa->email,
			'nome_destinatario' => $pessoa->nome,
			'titulo'			=> 'Ativação de usuário',
			'template'			=> $template
		);
		enviaEmail($dados_email);

		// Retorna mensagem de sucesso para tela de login
		$data['msg'] = "Efetuado o reenvio do email de ativação!";
		$data['msg_type'] = "primary";
		return redirect()->route('usuario')->with('data', $data);
	}

	/**
	 * Visualizar os dados do usuário logado
	 */
	public function meus_dados() {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao)) {
			return redirect()->route('login');
		}
		$data['msg'] = "";
		$data['msg_type'] = "";
		$data['errors'] = [];

		// Usuário
		$usuario = $this->usuario->where('id', $usuario_sessao->usuario->id)->first();
		if(!isset($usuario) || $usuario == '' || $usuario == null) {
			$data['msg'] = "Usuário não encontrado!";
			$data['msg_type'] = "danger";
			return redirect()->route('/')->with('data', $data);
		}

		// Pessoa
		$pessoa = $this->pessoa->where('id', $usuario->pessoa_id)->first();
		// Carrega perfil do usuario
		$perfil_usuario = $this->perfil->where('id', $usuario->perfil_id)->first();
		// Usuario Grupos
		$usuario_grupos = $this->usuarioGrupo->listar($usuario->id);

		if($this->request->getMethod() === 'post') {
		}

		$this->smarty->assign("usuario", $usuario);
		$this->smarty->assign("pessoa", $pessoa);
		$this->smarty->assign("perfil_usuario", $perfil_usuario);
		$this->smarty->assign("usuario_grupos", $usuario_grupos);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/usuario/meus_dados.tpl');
	}

	/**
	 * Teste de envio de email
	 * TODO: Migrar esse disparo para uma função que pode ser enviado um email para usuário
	 * via sistema
	 */
	public function mail() {
		$pessoa = $this->pessoa->where('email', 'williankaudy@gmail.com')->first();

		$usuario = $this->usuario->where('pessoa_id', $pessoa->id)->first();
		$link = url_to('usuario_ativar', $usuario->id, $usuario->chave_ativacao);

		// Monta os dados do template
		$dados_template = (object) array (
			"nome" => $pessoa->nome,
			"link_ativacao" => $link
		);
		$this->smarty->assign("dados_template", $dados_template);
		$template = $this->smarty->fetch($this->smarty->getTemplateDir(0) .'/emails/recuperar_senha.tpl');

		// Monta os dados do email
		$dados_email = (object) array(
			'email_destinatario'=> 'ti@paranaclube.com.br',
			'nome_destinatario' => 'Willian Kaudy',
			'titulo'			=> 'Email de teste 123',
			'corpo'				=> 'Teste envio de email pelo custom helper sem template',
			'template'			=> null
		);

		$dados_email->template = $template;
		//echo "<pre>";var_dump($dados_email);exit;
		enviaEmail($dados_email);
	}
}
