<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class Usuario extends Model {
	protected $table            = 'usuarios';
	protected $primaryKey       = 'id';
	protected $useAutoIncrement = true;
	protected $returnType       = 'object';
	protected $useSoftDeletes   = false;
	protected $protectFields    = false;
	protected $allowedFields    = ['pessoa_id','perfil_id', 'senha', 'status', 'chave_ativacao', 'data_cadastro', 'usuario_cadastro_id', 'data_ultimo_login', 'ip_ultimo_login'];

	// Dates
	protected $useTimestamps = false;
	protected $dateFormat    = 'datetime';
	protected $createdField  = 'data_cadastro';
	// protected $updatedField  = 'updated_at';
	// protected $deletedField  = 'deleted_at';

	// Validation
	// protected $validationRules      = [''];
	// protected $validationMessages   = [];
	// protected $skipValidation       = false;
	// protected $cleanValidationRules = true;

	// // Callbacks
	protected $allowCallbacks = true;
	protected $beforeInsert   = ['hashSenha', 'hashChaveAtivacao'];
	protected $afterInsert    = [];
	protected $beforeUpdate   = ['hashSenha', 'hashChaveAtivacao'];
	protected $afterUpdate    = [];
	protected $beforeFind     = [];
	protected $afterFind      = [];
	protected $beforeDelete   = [];
	protected $afterDelete    = [];

	// Models
	protected $modulo;
	protected $pessoa;
	protected $usuarioGrupo;

	protected function initialize() {
		$this->modulo = model(Modulo::class);
		$this->pessoa = model(Pessoa::class);
		$this->usuarioGrupo = model(UsuarioGrupo::class);
	}

	/**
	 * Transforma a senha em hash antes de ser gravado na base de dados
	 */
	protected function hashSenha($data) {
		if($data && isset($data['data']['senha'])) {
			$data['data']['senha'] = password_hash($data['data']['senha'], PASSWORD_DEFAULT);
		}
		return $data;
	}

	/**
	 * Transforma a chave de ativacao em hash, sha256, antes de ser gravado na base de dados
	 */
	protected function hashChaveAtivacao($data) {
		//&& $data['data']['status'] == 3
		if($data && isset($data['data']['chave_ativacao']) && $data['data']['chave_ativacao'] != null && $data['data']['chave_ativacao'] != '') {
			// Se for novo cadastro e estiver no status pendente, gera chave para ativacao
			$data['data']['chave_ativacao'] = hash('sha256', $data['data']['chave_ativacao']);
		}
		return $data;
	}

	/**
	 * Verifica se usuário está cadastrado e senha está correta
	 * Se tiver carrega os dados para sessão
	 */
	public function check($email=null, $senha=null) {
		$retorno = (object) array(
			"titulo" => "iPRC",
			"subtitulo" => "Sistema Integrado",
			"logado" => false,
			"usuario" => null,
			"perfil" => null,
			"modulos" => null
		);

		if($email != null && $senha != null) {
			$sql = "SELECT
						u.id,
						p.nome,
						p.email,
						p.documento,
						u.senha,
						ts.nome AS status,
						u.data_cadastro,
						u.perfil_id,
						pf.nome AS nome_perfil
					FROM
						usuarios u
							INNER JOIN
						pessoas p ON p.id = u.pessoa_id
							INNER JOIN
						tipos_status ts ON ts.id = u.status
							INNER JOIN
						perfis pf ON pf.id = u.perfil_id
					WHERE
						p.email='{$email}'
					LIMIT 1;";
			$query = $this->db->query($sql);
			$usuario = $query->getRow();

			if(is_null($usuario)) {
				return $retorno;
			}
			if(!password_verify($senha, $usuario->senha)) {
				return $retorno;
			}

			// Carrega os modulos e regras
			$modulos = $this->modulo->listarPorPefil($usuario->perfil_id);

			// Monta Retorno
			$retorno->logado = true;
			// usuario
			$retorno->usuario = (object) array(
				"id" => $usuario->id,
				"nome" => $usuario->nome,
				"email" => $usuario->email
			);
			// perfil
			$retorno->perfil = (object) array(
				"id" => $usuario->perfil_id,
				"nome" => $usuario->nome_perfil
			);
			// modulos
			$retorno->modulos = $modulos;
			// Regras

			// TODO: carregas as regras


			// Atualizada data do ultimo login e ip
			$data = array(
				"data_ultimo_login" => date('Y-m-d H:i:s'),
				"ip_ultimo_login" => getIpClient()
			);
			$this->update($usuario->id, $data);
		}

		return  $retorno;
	}

	/**
	 * Lista todos os usuários cadastrados
	 */
	public function listar($id=null, $email=null) {
		$sqlCpl = "";

		if($email != null) {
			$sqlCpl .= ((trim($sqlCpl) == '') ? ' WHERE ' : ' AND ').(" p.email='{$email}' ");
		}

		$sql = "SELECT
					u.id,
					p.nome,
					p.email,
					u.senha,
					p.documento,
					ts.nome AS status,
					u.data_cadastro
				FROM
					usuarios u
						INNER JOIN
					pessoas p ON p.id = u.pessoa_id
						INNER JOIN
					tipos_status ts ON ts.id = u.status
				{$sqlCpl}
				ORDER BY p.nome ASC;";
		$query = $this->db->query($sql);
		$result = $query->getResult();

		return $result;
	}

	/**
	 * Adiciona novo pessoa e usuário na base de dados
	 */
	public function adicionar($dados) {
		try {
			$this->db->transException(true)->transStart();

			$dados_pessoa = array(
				"documento" => $dados->documento,
				"tipo_documento" => $dados->tipo_documento,
				"nome" => $dados->nome,
				"sexo" => $dados->sexo,
				"data_nascimento" => $dados->data_nascimento,
				"estado_civil" => $dados->estado_civil,
				"telefone" => $dados->telefone,
				"email" => $dados->email,
				"status" => 1, // ativo
				"data_cadastro" => $dados->data_cadastro,
				"usuario_cadastro_id" => $dados->usuario_cadastro_id
			);
			$nova_pessoa_id = $this->pessoa->insert($dados_pessoa);
			if($nova_pessoa_id) {
				$timestamp = strtotime(date('Y-m-d H:i:s'));
				$dados_usuario = array(
					"pessoa_id" => $nova_pessoa_id,
					"perfil_id" => $dados->perfil_id,
					"senha" => $dados->senha,
					"status" => 3, // pendente
					"chave_ativacao" => "{$dados->chave_ativacao}{$timestamp}",
					"data_cadastro" => $dados->data_cadastro,
					"usuario_cadastro_id" => $dados->usuario_cadastro_id
				);
				$novo_usuario_id = $this->insert($dados_usuario);
				if($novo_usuario_id) {
					// Adiciona os Grupos do usuário
					if(count($dados->usuario_grupos) > 0) {
						foreach($dados->usuario_grupos as $c => $v) {
							$dados_usuario_grupo = array(
								"usuario_id" => $novo_usuario_id,
								"grupo_id" => $v,
								"status" => 1,
								"data_cadastro" => $dados->data_cadastro,
								"usuario_cadastro_id" => $dados->usuario_cadastro_id
							);
							$this->usuarioGrupo->insert($dados_usuario_grupo);
						}
					}
					$this->db->transComplete();
					return $novo_usuario_id;
				}else {
					$this->db->transRollback();
					return false;
				}
			}else {
				$this->db->transRollback();
				return false;
			}
		} catch (DatabaseException $e) {
			$this->db->transRollback();
			return $e;
		}
	}

	/**
	 * Atualiza dos dados do usuário e pessoa vinculada ao mesmo
	 */
	public function alterar($dados) {
		try {
			$this->db->transException(true)->transStart();


			$dados_pessoa = array(
				"documento" => $dados->documento,
				"tipo_documento" => $dados->tipo_documento,
				"nome" => $dados->nome,
				"sexo" => $dados->sexo,
				"data_nascimento" => $dados->data_nascimento,
				"estado_civil" => $dados->estado_civil,
				"telefone" => $dados->telefone,
				"email" => $dados->email,
				"data_alteracao" => $dados->data_alteracao,
				"usuario_alteracao_id" => $dados->usuario_alteracao_id
			);

			$pessoa_atualizada = $this->pessoa->update($dados->pessoa_id, $dados_pessoa);
			if($pessoa_atualizada) {
				$timestamp = strtotime(date('Y-m-d H:i:s'));
				$dados_usuario = array(
					"pessoa_id" => $dados->pessoa_id,
					"perfil_id" => $dados->perfil_id,
					"chave_ativacao" => "{$dados->chave_ativacao}{$timestamp}",
					"data_alteracao" => $dados->data_alteracao,
					"usuario_alteracao_id" => $dados->usuario_alteracao_id
				);
				if(isset($dados->senha) && $dados->senha != null) {
					$dados_usuario['senha'] = $dados->senha;
				}
				if(isset($dados->status) && $dados->status != null) {
					$dados_usuario['status'] = $dados->status;
				}

				$usuario_atualizado = $this->update($dados->usuario_id, $dados_usuario);
				if($usuario_atualizado) {
					if(count($dados->usuario_grupos) > 0) {
						// Inativa todos os grupos do usuário que não estiverem na lista
						$usuario_grupo_inativar = $this->usuarioGrupo->whereNotIn('grupo_id', $dados->usuario_grupos)->where('status', 1)->findAll();
						foreach($usuario_grupo_inativar as $c => $usuario_grupo) {
							$usuario_grupo->status = 4; // Excluido
							$usuario_grupo->data_alteracao = $dados->data_alteracao;
							$usuario_grupo->usuario_alteracao_id = $dados->usuario_alteracao_id;
							$this->usuarioGrupo->update($usuario_grupo->id, $usuario_grupo);
						}
						// Verifica todos os grupos ativos vinculados ao usuário
						foreach($dados->usuario_grupos as $c => $v) {
							$usuario_grupo_ativo = $this->usuarioGrupo->where('usuario_id', $dados->usuario_id)->where('grupo_id', $v)->where('status', 1)->find();
							if(count($usuario_grupo_ativo) == 0) {
								// Adiciona novo grupo ao usuário
								$dados_usuario_grupo = array(
									"usuario_id" => $dados->usuario_id,
									"grupo_id" => $v,
									"status" => 1,
									"data_cadastro" => $dados->data_alteracao,
									"usuario_cadastro_id" => $dados->usuario_alteracao_id
								);
								$this->usuarioGrupo->insert($dados_usuario_grupo);
							}
						}
					}
					$this->db->transComplete();
					return $usuario_atualizado;
				}else {
					$this->db->transRollback();
					return false;
				}
			}else {
				$this->db->transRollback();
				return false;
			}
		} catch (DatabaseException $e) {
			$this->db->transRollback();
			return $e;
		}
	}

	/**
	 * Ativar e troca senha usuário
	 */
	public function ativarUsuario($dados) {
		try {
			$this->db->transException(true)->transStart();
			$dados_usuario = array(
				"chave_ativacao" => $dados->chave_ativacao,
				"data_alteracao" => $dados->data_alteracao,
				"usuario_alteracao_id" => $dados->usuario_alteracao_id,
				"senha" => $dados->senha,
				"status" => $dados->status
			);
			$usuario_atualizado = $this->update($dados->usuario_id, $dados_usuario);
			if($usuario_atualizado) {
				$this->db->transComplete();
				return $usuario_atualizado;
			}else {
				$this->db->transRollback();
				return false;
			}
		} catch (DatabaseException $e) {
			$this->db->transRollback();
			return $e;
		}
	}

	/**
	 *	Verifica email e gera nova chave de ativação para troca de senha
	 */
	public function recuperarSenha($email) {
		$pessoa = $this->pessoa->where('email', $email)->first();
		if($pessoa && $pessoa->email == $email) {
			$usuario = $this->where('pessoa_id', $pessoa->id)->first();
			if($usuario) {
				$timestamp = strtotime(date('Y-m-d H:i:s'));
				$dados_usuario = array(
					"chave_ativacao" => "{$pessoa->documento}{$pessoa->email}{$timestamp}"
				);
				$this->update($usuario->id, $dados_usuario);
				return $pessoa = $this->pessoa->where('email', $email)->first();
			}
		}
		return false;
	}

}
