<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Reuniao;
use App\Models\Usuario;
use App\Models\Regra;
use App\Models\TipoStatus;
use App\Models\Grupo;
use App\Models\ReuniaoGrupo;
use App\Models\ReuniaoPresenca;
use App\Models\Documento;
use App\Models\UsuarioGrupo;
use App\Models\Pessoa;
use App\Models\EnvioEmail;

class ReuniaoC extends BaseController {

	protected $reuniao;
	protected $usuario;
	protected $regra;
	protected $tipoStatus;
	protected $grupo;
	protected $reuniaoGrupo;
	protected $reuniaoPresenca;
	protected $documento;
	protected $usuarioGrupo;
	protected $pessoa;
	protected $envioEmail;

	public function __construct() {
		$this->reuniao = model(Reuniao::class);
		$this->usuario = model(Usuario::class);
		$this->regra = model(Regra::class);
		$this->tipoStatus = model(TipoStatus::class);
		$this->grupo = model(Grupo::class);
		$this->reuniaoGrupo = model(ReuniaoGrupo::class);
		$this->reuniaoPresenca = model(ReuniaoPresenca::class);
		$this->documento = model(Documento::class);
		$this->usuarioGrupo = model(UsuarioGrupo::class);
		$this->pessoa = model(Pessoa::class);
		$this->envioEmail = model(EnvioEmail::class);
	}

	/**
	 * Lista as reuniões cadastradas
	 */
	public function index() {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao)) {
			$this->session->set('redirect_back', current_url());
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
			$titulo = $this->request->getPost('titulo');
			$status_id = $this->request->getPost('tipo_status_id');
			$grupo_id = $this->request->getPost('grupo_id');
			$data_reuniao_inicial = $this->request->getPost('data_reuniao_inicial');
			$data_reuniao_final = $this->request->getPost('data_reuniao_final');
			$filtros = array(
				"data_reuniao_inicial" => $data_reuniao_inicial,
				"data_reuniao_final" => $data_reuniao_final
			);
			$reunioes = $this->reuniao->listar(null, $titulo, $status_id, $grupo_id, $filtros);
		}else {
			$reunioes = $this->reuniao->listar(null, null, 1);
		}

		// Verifica permissões das reuniões
		foreach($reunioes as $c => $reuniao) {
			// Permite Cancelar
			if($this->regra->possuiRegra($usuario_sessao->usuario->id, 10) && $reuniao->status_id != 5 && $reuniao->status_id != 6) {
				$reunioes[$c]->permite_cancelar = true;
			}
			// Permite Alterar
			if(($this->regra->possuiRegra($usuario_sessao->usuario->id, 9)) && $reuniao->status_id == 3) { // status 3 - pendente
				$reunioes[$c]->permite_ativar = true;
			}
			// Permite Alterar
			if(($this->regra->possuiRegra($usuario_sessao->usuario->id, 9))) {
				$reunioes[$c]->permite_alterar = true;
			}
			if($reuniao->status_id == 1) {
				// Verifica s existe uma justificativa para o usuário logado e para essa reuniao?
				$presencas = $this->reuniaoPresenca->where('reuniao_id', $reuniao->id)->where('usuario_id', $usuario_sessao->usuario->id)->find();
				if(count($presencas) == 0) {
					$grupos_permitidos = $this->reuniaoPresenca->verificaPermissaoJustificativaUsuario($reuniao->id, $usuario_sessao->usuario->id);
					if(count($grupos_permitidos) > 0) {
						// Verifica horario maximo para justificativa da reunião
						$data_max_justificativa = date_sub(date_create($reuniao->data_reuniao), date_interval_create_from_date_string("10 minutes"));
						if(date('Y-m-d H:i:s') <= $data_max_justificativa->format('Y-m-d H:i:s')) {
							$reunioes[$c]->permite_justificar = true;
						}
					}
				}
			}
		}

		// Permite cadastrar reuniões
		$permite_cadastrar_reuniao = $this->regra->possuiRegra($usuario_sessao->usuario->id, 8);

		// Permite gerenciar presenças
		$permite_gerenciar_presencas = $this->regra->possuiRegra($usuario_sessao->usuario->id, 12);

		// Carrega os tipos de status
		$tipos_status = $this->tipoStatus->whereIn('id', array('1','3', '5', '6'))->findAll();
		// Carrega todos os grupos ativos
		$grupos = $this->grupo->where('status_id', 1)->findAll();

		//Permissões
		$this->smarty->assign("permite_cadastrar_reuniao", $permite_cadastrar_reuniao);
		$this->smarty->assign("permite_gerenciar_presencas", $permite_gerenciar_presencas);
		// Dados
		$this->smarty->assign("grupos", $grupos);
		$this->smarty->assign("tipos_status", $tipos_status);
		$this->smarty->assign("reunioes", $reunioes);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/reuniao/listar.tpl');
	}

	/**
	 * Cadastro da votação e grupo que poderao votar
	 */
	public function cadastrarReuniao() {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao) || !$this->regra->possuiRegra($usuario_sessao->usuario->id, 8)) {
			$this->session->set('redirect_back', current_url());
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
			$descricao = $this->request->getPost('descricao');
			$grupo_id = $this->request->getPost('grupo_id');
			$data_reuniao = $this->request->getPost('data_reuniao');
			$hora_reuniao = $this->request->getPost('hora_reuniao');
			$data_hora_reuniao = "{$data_reuniao} {$hora_reuniao}:00";
			if($status) {
				// Cria nova votação
				$dados = (object) array(
					"titulo" => $titulo,
					"descricao" => $descricao,
					"grupo_id" => $grupo_id,
					"data_reuniao" => $data_hora_reuniao,
					"data_cadastro" => date('Y-m-d H:i:s'),
					"usuario_cadastro_id" => $usuario_sessao->usuario->id
				);
				$nova_reuniao = $this->reuniao->adicionar($dados);
				if($nova_reuniao) {
					$data['msg'] = "Reunião cadastrada!";
					$data['msg_type'] = "primary";
					return redirect()->route('reuniao_cadastar_grupos', array($nova_reuniao))->with('data', $data);
				}else {
					$data['msg'] = "Reunião não cadastrada. Erros encontrados:";
					$data['msg_type'] = "danger";
					array_push($data['errors'], $nova_reuniao);
					$status = false;
				}
			}
		}

		// Carrega todos os grupos ativos
		$grupos = $this->grupo->where('status_id', 1)->findAll();

		$this->smarty->assign("grupos", $grupos);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/reuniao/cadastrar_reuniao.tpl');
	}

	/**
	 * Alteração da reunião, grupos e documentos
	 */
	public function alterarReuniao($reuniao_id) {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao) || !$this->regra->possuiRegra($usuario_sessao->usuario->id, 4)) {
			$this->session->set('redirect_back', current_url());
			return redirect()->route('login');
		}
		// mensagem temporaria da sessao
		$data = $this->session->getFlashdata('data');
		if(!isset($data)) {
			$data['msg'] = "";
			$data['msg_type'] = "";
			$data['errors'] = [];
		}

		// Carrega reuniao
		$reuniao = $this->reuniao->find($reuniao_id);
		if(!$reuniao) {
			return redirect()->route('reuniao');
		}

		if($this->request->getMethod() === 'post') {
			$titulo = $this->request->getPost('titulo');
			$descricao = $this->request->getPost('descricao');
			$grupo_id = $this->request->getPost('grupo_id');
			$data_reuniao = $this->request->getPost('data_reuniao');
			$hora_reuniao = $this->request->getPost('hora_reuniao');
			$data_hora_reuniao = "{$data_reuniao} {$hora_reuniao}:00";

			$dados = (object) array(
				"titulo" => $titulo,
				"descricao" => $descricao,
				"grupo_id" => $grupo_id,
				"data_reuniao" => $data_hora_reuniao,
				"data_alteracao" => date('Y-m-d H:i:s'),
				"usuario_alteracao_id" => $usuario_sessao->usuario->id
			);
			$status = $this->reuniao->update($reuniao_id, $dados);
			if($status) {
				$data['msg'] = "Reunião alterada!";
				$data['msg_type'] = "primary";
				return redirect()->route('reuniao_cadastar_grupos', array($reuniao_id))->with('data', $data);
			}else {
				$data['msg'] = "Votação não alterada. Erros encontrados:";
				$data['msg_type'] = "danger";
				array_push($data['errors'], $reuniao_id);
				$status = false;
			}
		}

		// Carrega todos os grupos ativos
		$grupos = $this->grupo->where('status_id', 1)->findAll();

		$this->smarty->assign("reuniao", $reuniao);
		$this->smarty->assign("grupos", $grupos);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/reuniao/alterar_reuniao.tpl');
	}

	/**
	 *	Cadastro dos grupos participantes da reunião
	 */
	public function cadastrarGrupos($reuniao_id) {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao)) {
			$this->session->set('redirect_back', current_url());
			return redirect()->route('login');
		}
		// mensagem temporaria da sessao
		$data = $this->session->getFlashdata('data');
		if(!isset($data)) {
			$data['msg'] = "";
			$data['msg_type'] = "";
			$data['errors'] = [];
		}
		// Carrega reuniao
		$reuniao = $this->reuniao->find($reuniao_id);
		if(!$reuniao) {
			return redirect()->route('reuniao');
		}

		if($this->request->getMethod() === 'post') {
			$status = true;
			$grupo_id = $this->request->getPost('grupo_id');
			$dados = (object) array(
				"reuniao_id" => $reuniao->id,
				"grupo_id" => $grupo_id,
				"status_id" => 1,
				"data_cadastro" => date('Y-m-d H:i:s'),
				"usuario_cadastro_id" => $usuario_sessao->usuario->id
			);
			$novo_grupo = $this->reuniaoGrupo->insert($dados);
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

		// Carrega todas as opçoes de grupos da reunião ativos
		$reuniao_grupos = $this->reuniaoGrupo->listar(null, $reuniao_id, null, 1);

		// Carrega todos os grupos ativos
		$grupos = $this->grupo->where('status_id', 1)->findAll();
		foreach($grupos as $c => $v) {
			foreach($reuniao_grupos as $c2 => $v2) {
				if($v->id == $v2->grupo_id) {
					unset($grupos[$c]);
				}
			}
		}

		$this->smarty->assign("grupos", $grupos);
		$this->smarty->assign("reuniao_grupos", $reuniao_grupos);
		$this->smarty->assign("reuniao", $reuniao);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/reuniao/cadastrar_grupos.tpl');
	}

	/**
	 * Remove o grupo informado da reunião
	 */
	public function removerGrupo($reuniao_id, $grupo_id) {
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
		// Carrega reuniao
		$reuniao = $this->reuniao->find($reuniao_id);
		if(!$reuniao) {
			return redirect()->route('reuniao');
		}else if($reuniao->status_id != 3) {
			$data['msg'] = "Reunião não pode ser mais alterada!";
			$data['msg_type'] = "primary";
			return redirect()->route('reuniao')->with('data', $data);
		}

		$grupo = $this->reuniaoGrupo->where('reuniao_id', $reuniao_id)->find($grupo_id);
		if(!$grupo) {
			return redirect()->route('reuniao');
		}else {
			$status = $this->reuniaoGrupo->where('reuniao_id', $reuniao_id)->delete($grupo_id);
			if($status) {
				$data['msg'] = "Grupo Removido!";
				$data['msg_type'] = "primary";
				return redirect()->route('reuniao_cadastar_grupos', array($reuniao_id))->with('data', $data);
			}else {
				$data['msg'] = "Erro ao tentar remover o grupo selecionado!";
				$data['msg_type'] = "danger";
				array_push($data['errors'], $status);
				return redirect()->route('reuniao_cadastar_grupos', array($reuniao_id))->with('data', $data);
			}
		}
	}

	/**
	 *	Visualiza os dados da reunião
	 */
	public function visualizar($reuniao_id) {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao)) {
			$this->session->set('redirect_back', current_url());
			return redirect()->route('login');
		}
		// mensagem temporaria da sessao
		$data = $this->session->getFlashdata('data');
		if(!isset($data)) {
			$data['msg'] = "";
			$data['msg_type'] = "";
			$data['errors'] = [];
		}

		// Carrega reuniao
		$reuniao = $this->reuniao->find($reuniao_id);
		if(!$reuniao) {
			return redirect()->route('reuniao');
		}

		if(count($this->request->getFiles()) > 0) {
			$validationRule = [
				'userfile' => [
					'rules' => [
						'uploaded[userfile]',
						'mime_in[userfile,image/jpg,image/jpeg,image/gif,image/png,image/webp,application/pdf]',
						'max_size[userfile,102400]',
					],
				],
			];
			if (! $this->validateData([], $validationRule)) {
				$data = ['errors' => $this->validator->getErrors()];

				$data['msg'] = "Ocorreu um problema ao tentar adicionar o documento.";
				$data['msg_type'] = "danger";
				return redirect()->route('reuniao_visualizar', array($reuniao_id))->with('data', $data);
			}
			$file = $this->request->getFile('userfile');
			$nome_arquivo = $file->getName();
			$ext = $file->getClientExtension();
			$timestamp = time();
			$arquivo_hash = hash('sha256', "reuniao_{$reuniao_id}_{$timestamp}");
			$newName = "{$arquivo_hash}.{$ext}";
			$file->move( './documentos/', $newName);

			// Persistir o documento e caminho no banco de dados
			// TODO: PREPARAR OS DADOS DO DOCUMENTO
			$dados = (object) array(
				"nome" => $nome_arquivo,
				"nome_arquivo" => $nome_arquivo,
				"tipo" => 'edital',
				"vinculo" => 'reunião',
				"referencia_id" => $reuniao_id,
				"status_id" => 1,
				"arquivo" => $newName,
				"hash" => $arquivo_hash,
				"extensao" => $ext,
				"data_cadastro" => date('Y-m-d H:i:s'),
				"usuario_cadastro_id" => $usuario_sessao->usuario->id
			);
			$novo_documento = $this->documento->insert($dados);
			if($novo_documento) {
				$data['msg'] = "Documento adicionado com sucesso";
				$data['msg_type'] = "primary";
			} else {
				$data['msg'] = "Ocorreu um problema ao tentar adicionar o documento. Erros encontrados:";
				$data['msg_type'] = "danger";
				array_push($data['errors'], $novo_documento);
				$status = false;
			}
		}

		// Grupo proprietário
		$grupo_proprietario = $this->grupo->find($reuniao->grupo_id);

		// Carrega os grupos vinculados a reunião
		$reuniao_grupos = $this->reuniaoGrupo->listar(null, $reuniao_id, null, 1);

		// Carrega os documentos vinculados a reunião
		$reuniao_documentos = $this->documento->listar(
			array(
				'vinculo' => 'reunião',
				'referencia_id' => $reuniao_id,
				'status_id' => '1',
				'valida_propriedade' => true,
				'proprietario_id' => $usuario_sessao->usuario->id
			)
		);

		// Presencas e justificativas
		if($this->regra->possuiRegra($usuario_sessao->usuario->id, 11)) {
			$presencas = $this->reuniaoPresenca->listar(null, $reuniao->id);
		} else {
			$presencas = $this->reuniaoPresenca->listar(null, $reuniao->id, null, null, $usuario_sessao->usuario->id);
		}

		// Permissões
		// Permite confirmar
		$permite_confirmar = $reuniao->status_id == 3 && $reuniao->usuario_cadastro_id == $usuario_sessao->usuario->id ? true : false;
		// Permite ativar reunião
		$permite_ativar = false;
		if($reuniao->status_id == 3 && (($reuniao->usuario_cadastro_id == $usuario_sessao->usuario->id) || $this->regra->possuiRegra($usuario_sessao->usuario->id, 15))) {
			$permite_ativar = true;
		}
		// Permite Altear
		$permite_alterar = false;
		if($reuniao->status_id == 3 && $this->regra->possuiRegra($usuario_sessao->usuario->id, 9)) {
			$permite_alterar = true;
		}
		// Permite adicionar documentos
		$permite_adicionar_documento = false;
		if($reuniao->usuario_cadastro_id == $usuario_sessao->usuario->id || $this->regra->possuiRegra($usuario_sessao->usuario->id, 13)) {
			$permite_adicionar_documento = true;
			$permite_remover_documento = true;
		}
		// Permite remover documentos
		$permite_remover_documento = false;
		if($reuniao->usuario_cadastro_id == $usuario_sessao->usuario->id || $this->regra->possuiRegra($usuario_sessao->usuario->id, 14)) {
			$permite_adicionar_documento = true;
			$permite_remover_documento = true;
		}
		// Justificativa Usuario
		$presenca_usuario = $this->reuniaoPresenca->where('reuniao_id', $reuniao->id)->where('usuario_id', $usuario_sessao->usuario->id)->find();

		// Permite justificar reunião
		$permite_justificar = false;
		// Verifica s existe uma justificativa para o usuário logado e para essa reuniao?
		if(count($presenca_usuario) == 0) {
			$grupos_permitidos = $this->reuniaoPresenca->verificaPermissaoJustificativaUsuario($reuniao->id, $usuario_sessao->usuario->id);
			if(count($grupos_permitidos) > 0) {
				// Verifica horario maximo para justificativa da reunião
				$data_max_justificativa = date_sub(date_create($reuniao->data_reuniao), date_interval_create_from_date_string("10 minutes"));
				if(date('Y-m-d H:i:s') <= $data_max_justificativa->format('Y-m-d H:i:s')) {
					$permite_justificar = true;
				}
			}
		}

		// Permissões
		$this->smarty->assign("permite_justificar", $permite_justificar);
		$this->smarty->assign("permite_alterar", $permite_alterar);
		$this->smarty->assign("permite_ativar", $permite_ativar);
		$this->smarty->assign("permite_confirmar", $permite_confirmar);
		$this->smarty->assign("permite_adicionar_documento", $permite_adicionar_documento);
		$this->smarty->assign("permite_remover_documento", $permite_remover_documento);
		// Dados
		$this->smarty->assign("presenca_usuario", $presenca_usuario);
		$this->smarty->assign("presencas", $presencas);
		$this->smarty->assign("reuniao", $reuniao);
		$this->smarty->assign("grupo_proprietario", $grupo_proprietario);
		$this->smarty->assign("reuniao_grupos", $reuniao_grupos);
		$this->smarty->assign("reuniao_documentos", $reuniao_documentos);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/reuniao/visualizar.tpl');
	}

	/**
	 *	Ativa a reunião
	 */
	public function ativarReuniao($reuniao_id) {
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

		// Carrega reuniao
		$reuniao = $this->reuniao->find($reuniao_id);
		if(!$reuniao || $reuniao->status_id != 3) {
			return redirect()->route('reuniao');
		}

		$dados = (object) array(
			"status_id" => 1, // ativo
			"data_ativacao" => date('Y-m-d H:i:s'),
			"usuario_ativacao_id" => $usuario_sessao->usuario->id,
			"status_envio_email" => 10 // a enviar
		);
		$status = $this->reuniao->update($reuniao_id, $dados);
		if($status) {
			$data['msg'] = "Reunião Ativada!";
			$data['msg_type'] = "primary";
			return redirect()->route('reuniao')->with('data', $data);
		}else {
			$data['msg'] = "Erro ao tentar ativar a votação selecionada(#{$reuniao_id})!";
			$data['msg_type'] = "danger";
			array_push($data['errors'], $status);
			return redirect()->route('reuniao')->with('data', $data);
		}
	}

	/**
	 *	Cancela a reunião
	 */
	public function cancelarReuniao($reuniao_id) {
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

		// Carrega reuniao
		$reuniao = $this->reuniao->find($reuniao_id);
		if(!$reuniao) {
			return redirect()->route('reuniao');
		}

		$dados = (object) array(
			"status_id" => 6, // cancelado
			"data_cancelamento" => date('Y-m-d H:i:s'),
			"usuario_cancelamento_id" => $usuario_sessao->usuario->id
		);
		$status = $this->reuniao->update($reuniao_id, $dados);
		if($status) {
			$data['msg'] = "Reunião Cancelada!";
			$data['msg_type'] = "primary";

			/** TODO: NO FUTURO, CRIAR DISPARO DE EMAILS A TODOS OS INTEGRANDES DOS GRUPOS QUANDO
			 * A REUNIAO FOR CANCELADA
			**/
			return redirect()->route('reuniao')->with('data', $data);
		}else {
			$data['msg'] = "Erro ao tentar ativar a votação selecionada(#{$reuniao_id})!";
			$data['msg_type'] = "danger";
			array_push($data['errors'], $status);
			return redirect()->route('reuniao')->with('data', $data);
		}
	}


	/**
	 * Justificar reunião
	 */
	public function justificarReuniao($reuniao_id, $usuario_id=null, $redirect=null) {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao)) {
			$this->session->set('redirect_back', current_url());
			return redirect()->route('login');
		}
		// mensagem temporaria da sessao
		$data = $this->session->getFlashdata('data');
		if(!isset($data)) {
			$data['msg'] = "";
			$data['msg_type'] = "";
			$data['errors'] = [];
		}

		// Carrega reuniao
		$reuniao = $this->reuniao->find($reuniao_id);
		if(!$reuniao) {
			return redirect()->route('reuniao');
		}
		$permite_gerenciar_presencas = $this->regra->possuiRegra($usuario_sessao->usuario->id, 12);

		// Usuario da justificativa
		$usuario_justificativa_id = $usuario_id != null && $permite_gerenciar_presencas ? $usuario_id : $usuario_sessao->usuario->id;
		// Verifica s existe uma justificativa para o usuário logado e para essa reuniao?
		$presencas = $this->reuniaoPresenca->where('reuniao_id', $reuniao->id)->where('usuario_id', $usuario_justificativa_id)->find();
		if($reuniao->status_id == 1) { // Status = 1: Ativo
			if(!$permite_gerenciar_presencas) {
				if(count($presencas) == 0) {
					$grupos_permitidos = $this->reuniaoPresenca->verificaPermissaoJustificativaUsuario($reuniao->id, $usuario_justificativa_id);
					if(count($grupos_permitidos) > 0) {
						// Verifica horario maximo para justificativa da reunião
						$data_max_justificativa = date_sub(date_create($reuniao->data_reuniao), date_interval_create_from_date_string("10 minutes"));
						if(date('Y-m-d H:i:s') <= $data_max_justificativa->format('Y-m-d H:i:s')) {
							//$reunioes[$c]->permite_justificar = true;
						}else {
							return redirect()->to(previous_url());
						}
					}else {
						return redirect()->to(previous_url());
					}
				}else {
					return redirect()->to(previous_url());
				}
			}else {
				if(count($presencas) > 0 && $presencas[0]->status_id == 9) {
					return redirect()->to(previous_url());
				}
			}
		}else {
			return redirect()->to(previous_url());
		}

		if($this->request->getMethod() === 'post') {
			if(!$presencas || count($presencas) == 0) {
				$dados = (object) array(
					"reuniao_id" => $reuniao->id,
					"usuario_id" => $usuario_justificativa_id,
					"justificativa" => $this->request->getPost('justificativa'),
					"status_id" => 9, // justificado
					"data_cadastro" => date('Y-m-d H:i:s'),
					"usuario_cadastro_id" => $usuario_sessao->usuario->id
				);
				$status = $this->reuniaoPresenca->insert($dados);
				if($status) {
					$data['msg'] = "Justificativa Adicionada!";
					$data['msg_type'] = "primary";
					if($redirect != null) {
						if($redirect == "gerenciar") {
							return redirect()->route('reuniao_presenca_gerenciar', array($reuniao_id))->with('data', $data);
						}
						return redirect()->to('reuniao')->with('data', $data);
					}else {
						return redirect()->to('reuniao')->with('data', $data);
					}
				}else {
					$data['msg'] = "Erro ao tentar justificar a reunião selecionada(#{$reuniao_id})!";
					$data['msg_type'] = "danger";
					array_push($data['errors'], $status);
					if($redirect != null) {
						if($redirect == "gerenciar") {
							return redirect()->route('reuniao_presenca_gerenciar', array($reuniao_id))->with('data', $data);
						}
						return redirect()->to('reuniao')->with('data', $data);
					}else {
						return redirect()->to('reuniao')->with('data', $data);
					}
				}
			}else {
				$dados = (object) array(
					"justificativa" => $this->request->getPost('justificativa'),
					"status_id" => 9, // justificado
					"data_alteracao" => date('Y-m-d H:i:s'),
					"usuario_alteracao_id" => $usuario_sessao->usuario->id
				);
				$status = $this->reuniaoPresenca->update($presencas[0]->id, $dados);
				if($status) {
					$data['msg'] = "Presença alterada!";
					$data['msg_type'] = "primary";
				}else {
					$data['msg'] = "Presença não alterada. Erros encontrados:";
					$data['msg_type'] = "danger";
					array_push($data['errors'], $presencas[0]->id);
					$status = false;
				}
				return redirect()->route('reuniao_presenca_gerenciar', array($reuniao_id))->with('data', $data);
			}
		}

		// Grupo proprietário
		$grupo_proprietario = $this->grupo->find($reuniao->grupo_id);

		$this->smarty->assign("grupo_proprietario", $grupo_proprietario);
		$this->smarty->assign("reuniao", $reuniao);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/reuniao/justificar_reuniao.tpl');
	}

	/**
	 * Gerenciar presenças de uma reunião
	 */
	public function gerenciarPresencasReuniao($reuniao_id) {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao)) {
			$this->session->set('redirect_back', current_url());
			return redirect()->route('login');
		}
		// mensagem temporaria da sessao
		$data = $this->session->getFlashdata('data');
		if(!isset($data)) {
			$data['msg'] = "";
			$data['msg_type'] = "";
			$data['errors'] = [];
		}
		$permite_gerenciar_presencas = $this->regra->possuiRegra($usuario_sessao->usuario->id, 12);

		// Carrega reuniao
		$reuniao = $this->reuniao->find($reuniao_id);
		if(!$reuniao || !$permite_gerenciar_presencas) {
			return redirect()->route('reuniao');
		}

		$participantes_reuniao = $this->reuniaoPresenca->listaTodosParticipantes($reuniao_id);

		// Grupo proprietário
		$grupo_proprietario = $this->grupo->find($reuniao->grupo_id);


		$this->smarty->assign("participantes_reuniao", $participantes_reuniao);
		$this->smarty->assign("grupo_proprietario", $grupo_proprietario);
		$this->smarty->assign("reuniao", $reuniao);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/reuniao/gerenciar_presenca_reuniao.tpl');
	}

	/**
	 *  Confirmação de presença ou ausencia do usuário na reunião
	 */
	public function confirmarPresencaReuniao($reuniao_id, $usuario_id, $acao='presente') {
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

		// Carrega reuniao
		$reuniao = $this->reuniao->find($reuniao_id);
		if(!$reuniao) {
			return redirect()->route('reuniao');
		}

		$presenca = $this->reuniaoPresenca->where('reuniao_id', $reuniao->id)->where('usuario_id', $usuario_id)->find();
		if($acao == 'presente') {
			if($presenca && count($presenca) > 0 && $presenca[0]->status_id != 7) {
				$dados = (object) array(
					"justificativa" => null,
					"status_id" => 7, // status_id = sim
					"data_alteracao" => date('Y-m-d H:i:s'),
					"usuario_alteracao_id" => $usuario_sessao->usuario->id
				);
				$status = $this->reuniaoPresenca->update($presenca[0]->id, $dados);
			}else if(count($presenca) == 0) {
				$dados = (object) array(
					"reuniao_id" => $reuniao->id,
					"usuario_id" => $usuario_id,
					"justificativa" => null,
					"status_id" => 7, // status_id = sim
					"data_cadastro" => date('Y-m-d H:i:s'),
					"usuario_cadastro_id" => $usuario_sessao->usuario->id
				);
				$status = $this->reuniaoPresenca->insert($dados);
			}else {
				return redirect()->route('reuniao_presenca_gerenciar', array($reuniao_id))->with('data', $data);
			}

			if($status) {
				$data['msg'] = "Presença alterada!";
				$data['msg_type'] = "primary";
			}else {
				$data['msg'] = "Presença não alterada. Erros encontrados:";
				$data['msg_type'] = "danger";
				array_push($data['errors'], $presenca[0]->id);
				$status = false;
			}
			return redirect()->route('reuniao_presenca_gerenciar', array($reuniao_id))->with('data', $data);

		}else if($acao == 'ausente') {
			if($presenca && count($presenca) > 0 && $presenca[0]->status_id != 8) {
				$dados = (object) array(
					"justificativa" => null,
					"status_id" => 8, // status_id = não
					"data_alteracao" => date('Y-m-d H:i:s'),
					"usuario_alteracao_id" => $usuario_sessao->usuario->id
				);
				$status = $this->reuniaoPresenca->update($presenca[0]->id, $dados);
			}else if(count($presenca) == 0) {
				$dados = (object) array(
					"reuniao_id" => $reuniao->id,
					"usuario_id" => $usuario_id,
					"justificativa" => null,
					"status_id" => 8, //  status_id = não
					"data_cadastro" => date('Y-m-d H:i:s'),
					"usuario_cadastro_id" => $usuario_sessao->usuario->id
				);
				$status = $this->reuniaoPresenca->insert($dados);
			}else {
				return redirect()->route('reuniao_presenca_gerenciar', array($reuniao_id))->with('data', $data);
			}

			if($status) {
				$data['msg'] = "Presença alterada!";
				$data['msg_type'] = "primary";
			}else {
				$data['msg'] = "Presença não alterada. Erros encontrados:";
				$data['msg_type'] = "danger";
				array_push($data['errors'], $presenca[0]->id);
				$status = false;
			}
			return redirect()->route('reuniao_presenca_gerenciar', array($reuniao_id))->with('data', $data);

		}else {
			return redirect()->route('reuniao_presenca_gerenciar', array($reuniao_id));
		}
	}

	/**
	 * Remove o documento informado da reunião
	 */
	public function removerDocumento($reuniao_id, $documento_id) {
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

		// Carrega reuniao
		$reuniao = $this->reuniao->find($reuniao_id);
		if(!$reuniao) {
			return redirect()->route('reuniao');
		}

		// Carrega documento
		$documento = $this->documento->find($documento_id);
		if(!$documento) {
			return redirect()->route('reuniao');
		}else {
			$dados = (object) array(
				"status_id" => 4, // excluido
				"data_alteracao" => date('Y-m-d H:i:s'),
				"usuario_alteracao_id" => $usuario_sessao->usuario->id
			);
			$status = $this->documento->update($documento_id, $dados);
			if($status) {
				$data['msg'] = "Documento removido!";
				$data['msg_type'] = "primary";
			}else {
				$data['msg'] = "Documento não removido. Erros encontrados:";
				$data['msg_type'] = "danger";
				array_push($data['errors'], $documento_id);
				$status = false;
			}
			return redirect()->route('reuniao_visualizar', array($reuniao_id))->with('data', $data);
		}
	}

	/**
	 *	Agenda o envio de email para os membros da reunão
	 */
	public function agendarEnvioEmailReuniao(){
		$reunioes = $this->reuniao->listar(null, null, 1, null, array("status_envio_email" => 10));

		if(count($reunioes) > 0) {
			foreach($reunioes as $reuniao) {
				if($reuniao->status_id == 1 && $reuniao->status_envio_email == 10) {
					$status = false;

					// Grupos da reunião
					$reuniao_grupos = $this->reuniaoGrupo->listar(null, $reuniao->id, null, 1);
					if(count($reuniao_grupos) > 0) {
						$usuarios_reuniao = array();
						foreach($reuniao_grupos as $reuniao_grupo) {

							// Usuarios do grupo
							$usuarios = $this->usuarioGrupo->listar(null, $reuniao_grupo->grupo_id);
							if(count($usuarios) > 0) {
								foreach($usuarios as $usuario) {

									// Verifica se o usuario já foi adicionado
									if(!in_array($usuario->usuario_id, $usuarios_reuniao)) {
										$usuarios_reuniao[] = $usuario->usuario_id;

										$pessoa = $this->pessoa->find($usuario->pessoa_id);
										$link = url_to('reuniao_visualizar', $reuniao->id);
										$template = '/emails/reuniao_conselho.tpl';

										// Monta os dados do template
										$payload = (object) array (
											'titulo'	=> "Edital de Convocação - {$reuniao->titulo}",
											"nome" 		=> primeiroNome($pessoa->nome),
											"descricao" => $reuniao->descricao,
											"link" 		=> $link
										);

										// Adiciona agendamento
										$dados = (object) array(
											"status_id" => 3, // pendente
											"destinatario" => $pessoa->email,
											"titulo" => "Edital de Convocação - {$reuniao->titulo}",
											"template" => $template,
											"payload" => json_encode($payload),
											"tentativa_reenvio" => 0,
											"data_cadastro" => date('Y-m-d H:i:s'),
											"usuario_cadastro_id" => 1
										);
										$status = $this->envioEmail->insert( $dados);
									}
								}
							}
						}
					}

					// Atualiza status da reunião
					if($status) {
						$dados = (object) array(
							"status_envio_email" => 11, // enviado
							"data_alteracao" => date('Y-m-d H:i:s'),
							"usuario_alteracao_id" => 1
						);
						$this->reuniao->update($reuniao->id, $dados);
					}
				}
			}
		}
	}

	public function listarReunioes($grupo_id = null) {

		if($this->request->getMethod() === 'post') {
		}
		$reunioes = $this->reuniao->listar(null, null, 1, $grupo_id);
		echo json_encode($reunioes);
	}
}