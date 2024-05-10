<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Usuario;
use App\Models\Grupo;
use App\Models\TipoStatus;
use App\Models\Votacao;
use App\Models\VotacaoOpcao;
use App\Models\VotacaoGrupo;
use App\Models\VotacaoFiscal;
use App\Models\Voto;
use App\Models\Regra;

class VotacaoC extends BaseController {

	protected $usuario;
	protected $votacao;
	protected $votacaoOpcao;
	protected $votacaoGrupo;
	protected $votacaoFiscal;
	protected $voto;
	protected $grupo;
	protected $tipoStatus;
	protected $regra;

	public function __construct() {
		$this->usuario = model(Usuario::class);
		$this->votacao = model(Votacao::class);
		$this->votacaoOpcao = model(VotacaoOpcao::class);
		$this->votacaoGrupo = model(VotacaoGrupo::class);
		$this->grupo = model(Grupo::class);
		$this->tipoStatus = model(TipoStatus::class);
		$this->votacaoFiscal = model(VotacaoFiscal::class);
		$this->voto = model(Voto::class);
		$this->regra = model(Regra::class);
	}

	/**
	 * Lista as votações
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

		if($this->request->getMethod() === 'post') {
			$titulo = $this->request->getPost('titulo') != '' ? $this->request->getPost('titulo') : null;
			$tipo_status_id = $this->request->getPost('tipo_status_id') != '' ? $this->request->getPost('tipo_status_id') : null;
			// Carrega lista de votacoes
			$votacoes = $this->votacao->listar(null, $titulo, $tipo_status_id, $usuario_sessao->usuario->id);
		}else {
			// Carrega lista de votacoes
			$votacoes = $this->votacao->listar(null, null, 1);
		}
		// Verifica permissões das votações
		foreach($votacoes as $c => $votacao) {
			$permissao_grupo = $this->votacaoGrupo->verificaPermissaoGrupo($votacao->id, $usuario_sessao->usuario->id);
			$voto_realizado = $this->voto->where('votacao_id', $votacao->id)->where('usuario_id', $usuario_sessao->usuario->id)->find();
			// Permite votar
			if($votacao->status_id == 1 && $permissao_grupo && !$voto_realizado) {
				$votacoes[$c]->permite_votar = true;
			}
			// Fiscal da votação
			$fiscal_votacao = $this->votacaoFiscal->where('votacao_id', $votacao->id)->where('usuario_id', $usuario_sessao->usuario->id)->find();
			// Permite ver resultado
			if(($fiscal_votacao && $votacao->status_id != 3) || $votacao->status_id == 5) { // status 5 - finalizado
				$votacoes[$c]->permite_resultado = true;
			}
			// Permite Cancelar
			if(($fiscal_votacao || $this->regra->possuiRegra($usuario_sessao->usuario->id, 3)) && $votacao->status_id == 3) { // status 3 - pendente
				$votacoes[$c]->permite_cancelar = true;
			}
			// Permite Alterar
			if(($fiscal_votacao || $this->regra->possuiRegra($usuario_sessao->usuario->id, 4)) && $votacao->status_id == 3) { // status 3 - pendente
				$votacoes[$c]->permite_alterar = true;
			}
			// Permite Alterar
			if($fiscal_votacao && $votacao->status_id == 3) { // status 3 - pendente
				$votacoes[$c]->permite_ativar = true;
			}
		}

		// Carrega os tipos de status
		$tipos_status = $this->tipoStatus->whereIn('id', array('1','3', '5'))->findAll();
		// Permite cadastrar votação
		$permite_cadastrar_votacao = $this->regra->possuiRegra($usuario_sessao->usuario->id, 2);

		$this->smarty->assign("permite_cadastrar_votacao", $permite_cadastrar_votacao);
		$this->smarty->assign("tipos_status", $tipos_status);
		$this->smarty->assign("votacoes", $votacoes);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/votacao/listar.tpl');
	}

	/**
	 * Cadastro da votação e grupo que poderao votar
	 */
	public function cadastrarVotacao() {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao) || !$this->regra->possuiRegra($usuario_sessao->usuario->id, 2)) {
			return redirect()->route('login');
		}
		// mensagem temporaria da sessao
		$data = $this->session->getFlashdata('data');
		if(!isset($data)) {
			$data['msg'] = "";
			$data['msg_type'] = "";
			$data['errors'] = [];
		}

		if($this->request->getMethod() === 'post') {
			$status = true;

			$titulo = $this->request->getPost('titulo');
			$texto = $this->request->getPost('texto');
			$qtd_escolhas = $this->request->getPost('qtd_escolhas') != '' ? $this->request->getPost('qtd_escolhas') : 1;

			if($status) {
				// Cria nova votação
				$dados = (object) array(
					"titulo" => $titulo,
					"texto" => $texto,
					"qtd_escolhas" => $qtd_escolhas,
					"data_cadastro" => date('Y-m-d H:i:s'),
					"usuario_cadastro_id" => $usuario_sessao->usuario->id
				);
				$nova_votacao = $this->votacao->adicionar($dados);
				if($nova_votacao) {
					$data['msg'] = "Votação cadastrada!";
					$data['msg_type'] = "primary";
					return redirect()->route('votacao_cadastar_opcoes', array($nova_votacao))->with('data', $data);
				}else {
					$data['msg'] = "Votação não cadastrada. Erros encontrados:";
					$data['msg_type'] = "danger";
					array_push($data['errors'], $nova_votacao);
					$status = false;
				}
			}
		}

		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/votacao/cadastrar_votacao.tpl');
	}

	/**
	 * Cadastro da votação e grupo que poderao votar
	 */
	public function alterarVotacao($votacao_id) {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao) || !$this->regra->possuiRegra($usuario_sessao->usuario->id, 4)) {
			return redirect()->route('login');
		}
		// mensagem temporaria da sessao
		$data = $this->session->getFlashdata('data');
		if(!isset($data)) {
			$data['msg'] = "";
			$data['msg_type'] = "";
			$data['errors'] = [];
		}

		// Carrega votacao
		$votacao = $this->votacao->find($votacao_id);
		if(!$votacao) {
			return redirect()->route('votacao');
		}else if($votacao->status != 3) {
			$data['msg'] = "Votação não pode ser mais alterada!";
			$data['msg_type'] = "primary";
			return redirect()->route('votacao')->with('data', $data);
		}

		if($this->request->getMethod() === 'post') {
			$titulo = $this->request->getPost('titulo');
			$texto = $this->request->getPost('texto');
			$qtd_escolhas = $this->request->getPost('qtd_escolhas') != '' ? $this->request->getPost('qtd_escolhas') : 1;

			$dados = (object) array(
				"titulo" => $titulo,
				"texto" => $texto,
				"qtd_escolhas" => $qtd_escolhas,
				"data_alteracao" => date('Y-m-d H:i:s'),
				"usuario_alteracao_id" => $usuario_sessao->usuario->id
			);
			$status = $this->votacao->update($votacao_id, $dados);
			if($status) {
				$data['msg'] = "Votação alterada!";
				$data['msg_type'] = "primary";
				return redirect()->route('votacao_cadastar_opcoes', array($votacao_id))->with('data', $data);
			}else {
				$data['msg'] = "Votação não alterada. Erros encontrados:";
				$data['msg_type'] = "danger";
				array_push($data['errors'], $votacao_id);
				$status = false;
			}
		}

		// Carrega todos os grupos ativos
		$grupos = $this->grupo->where('status', 1)->findAll();

		$this->smarty->assign("votacao", $votacao);
		$this->smarty->assign("grupos", $grupos);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/votacao/alterar_votacao.tpl');
	}

	/**
	 *	Cadastro dos grupos com permissão a votação
	 */
	public function cadastrarGrupos($votacao_id) {
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
		// Carrega votacao
		$votacao = $this->votacao->find($votacao_id);
		if(!$votacao) {
			return redirect()->route('votacao');
		}else if($votacao->status != 3) {
			$data['msg'] = "Votação não pode ser mais alterada!";
			$data['msg_type'] = "primary";
			return redirect()->route('votacao')->with('data', $data);
		}

		if($this->request->getMethod() === 'post') {
			$status = true;
			$grupo_id = $this->request->getPost('grupo_id');
			$dados = (object) array(
				"votacao_id" => $votacao->id,
				"grupo_id" => $grupo_id,
				"status" => 1,
				"data_cadastro" => date('Y-m-d H:i:s'),
				"usuario_cadastro_id" => $usuario_sessao->usuario->id
			);
			$novo_grupo = $this->votacaoGrupo->insert($dados);
			if($novo_grupo) {
				$data['msg'] = "Grupo cadastrado!";
				$data['msg_type'] = "primary";
			} else {
				$data['msg'] = "Grupo não cadastrado. Erros encontrados:";
				$data['msg_type'] = "danger";
				array_push($data['errors'], $novo_grupo);
				$status = false;
			}
		}

		// Carrega todas as opçoes de votacao ativas
		$votacao_grupos = $this->votacaoGrupo->listar(null, $votacao_id, null, 1);

		// Carrega todos os grupos ativos
		$grupos = $this->grupo->where('status', 1)->findAll();
		foreach($grupos as $c => $v) {
			foreach($votacao_grupos as $c2 => $v2) {
				if($v->id == $v2->grupo_id) {
					unset($grupos[$c]);
				}
			}
		}

		$this->smarty->assign("grupos", $grupos);
		$this->smarty->assign("votacao_grupos", $votacao_grupos);
		$this->smarty->assign("votacao", $votacao);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/votacao/cadastrar_grupos.tpl');
	}

	/**
	 * Remove o grupo informado da votação
	 */
	public function removerGrupo($votacao_id, $grupo_id) {
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
		// Carrega votacao
		$votacao = $this->votacao->find($votacao_id);
		if(!$votacao) {
			return redirect()->route('votacao');
		}else if($votacao->status != 3) {
			$data['msg'] = "Votação não pode ser mais alterada!";
			$data['msg_type'] = "primary";
			return redirect()->route('votacao')->with('data', $data);
		}

		$grupo = $this->votacaoGrupo->where('votacao_id', $votacao_id)->find($grupo_id);
		if(!$grupo) {
			return redirect()->route('votacao');
		}else {
			$status = $this->votacaoGrupo->where('votacao_id', $votacao_id)->delete($grupo_id);
			if($status) {
				$data['msg'] = "Grupo Removido!";
				$data['msg_type'] = "primary";
				return redirect()->route('votacao_cadastar_grupos', array($votacao_id))->with('data', $data);
			}else {
				$data['msg'] = "Erro ao tentar remover o grupo selecionado!";
				$data['msg_type'] = "danger";
				array_push($data['errors'], $status);
				return redirect()->route('votacao_cadastar_grupos', array($votacao_id))->with('data', $data);
			}
		}
	}

	/**
	 *	Cadastro as opções da votação
	 */
	public function cadastrarOpcoes($votacao_id) {
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
		// Carrega votacao
		$votacao = $this->votacao->find($votacao_id);
		if(!$votacao) {
			return redirect()->route('votacao');
		}else if($votacao->status != 3) {
			$data['msg'] = "Votação não pode ser mais alterada!";
			$data['msg_type'] = "primary";
			return redirect()->route('votacao')->with('data', $data);
		}

		if($this->request->getMethod() === 'post') {
			$status = true;
			$opcao = $this->request->getPost('opcao');
			$dados = (object) array(
				"titulo" => $opcao,
				"votacao_id" => $votacao_id,
				"data_cadastro" => date('Y-m-d H:i:s'),
				"usuario_cadastro_id" => $usuario_sessao->usuario->id
			);
			$nova_opcao = $this->votacaoOpcao->insert($dados);
			if($nova_opcao) {
				$data['msg'] = "Opção cadastrada!";
				$data['msg_type'] = "primary";
			} else {
				$data['msg'] = "Opção não cadastrada. Erros encontrados:";
				$data['msg_type'] = "danger";
				array_push($data['errors'], $nova_opcao);
				$status = false;
			}
		}

		// Carrega todas as opçoes de votacao ativas
		$votacao_opcoes = $this->votacaoOpcao->where('votacao_id', $votacao_id)->orderBy('titulo', 'asc')->findAll();

		$this->smarty->assign("votacao_opcoes", $votacao_opcoes);
		$this->smarty->assign("votacao", $votacao);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/votacao/cadastrar_opcoes.tpl');
	}

	/**
	 * Remove opção informada
	 */
	public function removerOpcao($votacao_id, $opcao_id) {
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
		// Carrega votacao
		$votacao = $this->votacao->find($votacao_id);
		if(!$votacao) {
			return redirect()->route('votacao');
		}else if($votacao->status != 3) {
			$data['msg'] = "Votação não pode ser mais alterada!";
			$data['msg_type'] = "primary";
			return redirect()->route('votacao')->with('data', $data);
		}

		$opcao = $this->votacaoOpcao->where('votacao_id', $votacao_id)->find($opcao_id);
		if(!$opcao) {
			return redirect()->route('votacao');
		}else {
			$status = $this->votacaoOpcao->where('votacao_id', $votacao_id)->delete($opcao_id);
			if($status) {
				$data['msg'] = "Opção Removida!";
				$data['msg_type'] = "primary";
				return redirect()->route('votacao_cadastar_opcoes', array($votacao_id))->with('data', $data);
			}else {
				$data['msg'] = "Erro ao tentar remover a opção selecionada!";
				$data['msg_type'] = "danger";
				array_push($data['errors'], $status);
				return redirect()->route('votacao_cadastar_opcoes', array($votacao_id))->with('data', $data);
			}
		}
	}

	/**
	 *	Cadastro dos fiscais da votação
	 */
	public function cadastrarFiscais($votacao_id) {
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
		// Carrega votacao
		$votacao = $this->votacao->find($votacao_id);
		if(!$votacao) {
			return redirect()->route('votacao');
		}else if($votacao->status != 3) {
			$data['msg'] = "Votação não pode ser mais alterada!";
			$data['msg_type'] = "primary";
			return redirect()->route('votacao')->with('data', $data);
		}

		if($this->request->getMethod() === 'post') {
			$status = true;
			$usuario_id = $this->request->getPost('usuario_id');
			$dados = (object) array(
				"usuario_id" => $usuario_id,
				"votacao_id" => $votacao_id,
				"status"	 => 1,
				"data_cadastro" => date('Y-m-d H:i:s'),
				"usuario_cadastro_id" => $usuario_sessao->usuario->id
			);
			$novo_fiscal = $this->votacaoFiscal->insert($dados);
			if($novo_fiscal) {
				$data['msg'] = "Fiscal cadastrado!";
				$data['msg_type'] = "primary";
			} else {
				$data['msg'] = "Fiscal não cadastrado. Erros encontrados:";
				$data['msg_type'] = "danger";
				array_push($data['errors'], $nova_opcao);
				$status = false;
			}
		}

		// Carrega lista de usuários
		$fiscais = $this->usuario->listar();
		// Carrega todas as opçoes de votacao ativas
		$votacao_fiscais = $this->votacaoFiscal->listar(null, $votacao_id, null, 1);

		$this->smarty->assign("votacao_fiscais", $votacao_fiscais);
		$this->smarty->assign("votacao", $votacao);
		$this->smarty->assign("fiscais", $fiscais);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/votacao/cadastrar_fiscais.tpl');
	}

	/**
	 * Remove opção informada
	 */
	public function removerFiscal($votacao_id, $fiscal_id) {
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

		// Carrega votacao
		$votacao = $this->votacao->find($votacao_id);
		if(!$votacao) {
			return redirect()->route('votacao');
		}else if($votacao->status != 3) {
			$data['msg'] = "Votação não pode ser mais alterada!";
			$data['msg_type'] = "primary";
			return redirect()->route('votacao')->with('data', $data);
		}

		$fiscal = $this->votacaoFiscal->where('votacao_id', $votacao_id)->find($fiscal_id);
		if(!$fiscal) {
			return redirect()->route('votacao');
		}else {
			$status = $this->votacaoFiscal->where('votacao_id', $votacao_id)->delete($fiscal_id);
			if($status) {
				$data['msg'] = "Fiscal Removido!";
				$data['msg_type'] = "primary";
				return redirect()->route('votacao_cadastar_fiscais', array($votacao_id))->with('data', $data);
			}else {
				$data['msg'] = "Erro ao tentar remover o fiscal selecionado!";
				$data['msg_type'] = "danger";
				array_push($data['errors'], $status);
				return redirect()->route('votacao_cadastar_fiscais', array($votacao_id))->with('data', $data);
			}
		}
	}

	/**
	 *	Visualiza dados da votação
	 */
	public function visualizar($votacao_id) {
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

		// Carrega votacao
		$votacao = $this->votacao->find($votacao_id);
		if(!$votacao) {
			return redirect()->route('votacao');
		}
		// Carrega todas as opçoes de votacao ativas
		$votacao_opcoes = $this->votacaoOpcao->where('votacao_id', $votacao_id)->orderBy('titulo', 'asc')->findAll();
		// Carrega os grupos vinculados a votação
		$votacao_grupos = $this->votacaoGrupo->listar(null, $votacao_id, null, 1);
		// Carrega os fiscais vinculados a votação
		$votacao_fiscais = $this->votacaoFiscal->listar(null, $votacao_id, null, 1);

		if($this->request->getMethod() === 'post') {
		}

		// Permissões
		// Fiscal da votação
		$fiscal_votacao = $this->votacaoFiscal->where('votacao_id', $votacao->id)->where('usuario_id', $usuario_sessao->usuario->id)->find();
		// Permite confirmar
		$permite_confirmar = $votacao->status == 3 && $votacao->usuario_cadastro_id == $usuario_sessao->usuario->id ? true : false;
		// Permite ativar votação
		$permite_ativar = false;
		if($votacao->status == 3 && ($fiscal_votacao || $votacao->usuario_cadastro_id == $usuario_sessao->usuario->id)) {
			$permite_ativar = true;
		}
		$permite_alterar = false;
		if($votacao->status == 3 && ($fiscal_votacao || $votacao->usuario_cadastro_id == $usuario_sessao->usuario->id)) {
			$permite_alterar = true;
		}

		$this->smarty->assign("permite_alterar", $permite_alterar);
		$this->smarty->assign("permite_ativar", $permite_ativar);
		$this->smarty->assign("permite_confirmar", $permite_confirmar);
		$this->smarty->assign("votacao", $votacao);
		$this->smarty->assign("votacao_opcoes", $votacao_opcoes);
		$this->smarty->assign("votacao_grupos", $votacao_grupos);
		$this->smarty->assign("votacao_fiscais", $votacao_fiscais);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/votacao/visualizar.tpl');
	}

	/**
	 *	Ativa a votação para ser votada
	 */
	public function ativarVotacao($votacao_id) {
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

		// Carrega votacao
		$votacao = $this->votacao->find($votacao_id);
		if(!$votacao || $votacao->status != 3) {
			return redirect()->route('votacao');
		}

		$dados = (object) array(
			"status" => 1, // ativo
			"data_ativacao" => date('Y-m-d H:i:s'),
			"usuario_ativacao_id" => $usuario_sessao->usuario->id
		);
		$status = $this->votacao->update($votacao_id, $dados);
		if($status) {
			$data['msg'] = "Votação Ativada!";
			$data['msg_type'] = "primary";
			return redirect()->route('votacao')->with('data', $data);
		}else {
			$data['msg'] = "Erro ao tentar ativar a votação selecionada(#{$votacao_id})!";
			$data['msg_type'] = "danger";
			array_push($data['errors'], $status);
			return redirect()->route('votacao')->with('data', $data);
		}
	}

	/**
	 * Ação de Votar
	 */
	public function votar($votacao_id) {
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

		// Carrega votacao
		$votacao = $this->votacao->find($votacao_id);
		if(!$votacao) {
			return redirect()->route('votacao');
		}
		// Verifica permissão de grupo do usuario
		$permissao_grupo = $this->votacaoGrupo->verificaPermissaoGrupo($votacao_id, $usuario_sessao->usuario->id);
		if(!$permissao_grupo) {
			return redirect()->route('votacao');
		}
		// Carrega voto do usuário
		$voto_realizado = $this->voto->where('votacao_id', $votacao_id)->where('usuario_id', $usuario_sessao->usuario->id)->find();
		if($voto_realizado) {
			$data['msg'] = "Voto já computado para o usuário!";
			$data['msg_type'] = "warning";
			return redirect()->route('votacao')->with('data', $data);
		}

		if($this->request->getMethod() === 'post') {
			$voto = $this->request->getPost('voto');
			if($voto != null & $voto != '') {
				$dados = (object) array(
					"votacao_id" => $votacao_id,
					"usuario_id" => $usuario_sessao->usuario->id,
					"voto" => $voto,
					"data_voto" => date('Y-m-d H:i:s'),
					"ip_usuario" => getIpClient()
				);
				$status = $this->voto->insert($dados);
				if($status) {
					$data['msg'] = "Voto efetuado com sucesso!";
					$data['msg_type'] = "primary";
					return redirect()->route('votacao')->with('data', $data);
				}else {
					$data['msg'] = "Erro ao validar voto. Por favor tente novamente!";
					$data['msg_type'] = "danger";
					array_push($data['errors'], $status);
					return redirect()->route('votacao')->with('data', $data);
				}
			}
		}

		// Carrega todas as opçoes de votacao ativas
		$votacao_opcoes = $this->votacaoOpcao->where('votacao_id', $votacao_id)->orderBy('titulo', 'asc')->findAll();
		// Carrega os grupos vinculados a votação
		$votacao_grupos = $this->votacaoGrupo->listar(null, $votacao_id, null, 1);

		$this->smarty->assign("votacao", $votacao);
		$this->smarty->assign("votacao_opcoes", $votacao_opcoes);
		$this->smarty->assign("votacao_grupos", $votacao_grupos);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/votacao/votar.tpl');
	}

	/**
	 * Resultado da votação
	 */
	public function resultado($votacao_id) {
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

		// Carrega votacao
		$votacao = $this->votacao->find($votacao_id);
		if(!$votacao) {
			return redirect()->route('votacao');
		}
		// verifica se usuário é fiscal
		$fiscal_votacao = $this->votacaoFiscal->where('votacao_id', $votacao_id)->where('usuario_id', $usuario_sessao->usuario->id)->find();
		if(!$votacao) {
			return redirect()->route('votacao');
		}

		// Carrega todas as opçoes de votacao ativas
		$votacao_opcoes = $this->votacaoOpcao->where('votacao_id', $votacao_id)->orderBy('titulo', 'asc')->findAll();
		// Carrega os grupos vinculados a votação
		$votacao_grupos = $this->votacaoGrupo->listar(null, $votacao_id, null, 1);
		// Carrega os fiscais vinculados a votação
		$votacao_fiscais = $this->votacaoFiscal->listar(null, $votacao_id, null, 1);
		// Carrega o resultado da votação
		$resultado_opcoes = $this->votacaoOpcao->listarResultadoOpcoes($votacao_id);

		$this->smarty->assign("votacao", $votacao);
		$this->smarty->assign("votacao_opcoes", $votacao_opcoes);
		$this->smarty->assign("votacao_grupos", $votacao_grupos);
		$this->smarty->assign("votacao_fiscais", $votacao_fiscais);
		$this->smarty->assign("resultado_opcoes", $resultado_opcoes);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/votacao/resultado.tpl');
	}
}
