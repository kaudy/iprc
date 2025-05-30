<?php

namespace App\Models;

use CodeIgniter\Model;

class Votacao extends Model
{
    protected $table            = 'votacoes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'data_cadastro';
    protected $updatedField  = 'data_alteracao';
    // protected $deletedField  = 'deleted_at';

    // // Validation
    // protected $validationRules      = [];
    // protected $validationMessages   = [];
    // protected $skipValidation       = false;
    // protected $cleanValidationRules = true;

    // // Callbacks
    // protected $allowCallbacks = true;
    // protected $beforeInsert   = [];
    // protected $afterInsert    = [];
    // protected $beforeUpdate   = [];
    // protected $afterUpdate    = [];
    // protected $beforeFind     = [];
    // protected $afterFind      = [];
    // protected $beforeDelete   = [];
    // protected $afterDelete    = [];

	// Models
	protected $votacaoGrupo;

	protected function initialize() {
		$this->votacaoGrupo = model(VotacaoGrupo::class);
	}

	/**
	 * Adiciona nova votação a base de dados
	 */
	public function adicionar($dados) {
		try {
			$this->db->transException(true)->transStart();
			/*"titulo" => $titulo,
					"texto" => $texto,
					"qtd_escolhas" => $qtd_escolhas,
					"votacao_grupos" => $votacao_grupos,
					"data_cadastro" => date('Y-m-d H:i:s'),
					"usuario_cadastro_id" => $usuario_sessao->usuario->id*/


			$dados_votacao = array(
				"titulo" => $dados->titulo,
				"texto" => $dados->texto,
				"qtd_escolhas" => $dados->qtd_escolhas,
				"status_id" => 3, // pendente
				"data_cadastro" => $dados->data_cadastro,
				"usuario_cadastro_id" => $dados->usuario_cadastro_id
			);
			$nova_votacao_id = $this->insert($dados_votacao);
			if($nova_votacao_id) {
				$this->db->transComplete();
				return $nova_votacao_id;
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
	 * Lista todas as votações cadastradas
	 */
	public function listar($id=null, $titulo=null, $status_id=null, $votante_usuario_id=null, $usuario_fiscal_id=null, $usuario_cadastro_id=null) {
		$sqlCpl = "";
		$sqlCpl2 = "";

		if($id != null) {
			$sqlCpl .= " AND v.id='{$id}' ";
		}
		if($status_id != null) {
			$sqlCpl .= "AND v.status_id={$status_id} ";
		}
		if($titulo != null) {
			$sqlCpl .= "AND v.titulo like '%{$titulo}%' ";
		}
		if($usuario_fiscal_id != null) {
			$sqlCpl2 .= " AND ( ({$usuario_fiscal_id} IS NULL OR vf.usuario_id={$usuario_fiscal_id}) ";
		}
		if($usuario_cadastro_id != null) {
			$sqlCpl2 .= ($sqlCpl2 == null ? "AND ( " : " OR ") . " ({$usuario_fiscal_id} IS NULL OR v.usuario_cadastro_id={$usuario_cadastro_id}) ";
		}
		if($votante_usuario_id != null) {
			$sqlCpl2 .= ($sqlCpl2 == null ? "AND ( " : "OR ") . " ({$votante_usuario_id} IS NULL OR ug.usuario_id={$votante_usuario_id}) ";
		}
		if($sqlCpl2 != null) {
			$sqlCpl2 .= ")";
		}

		$sql = "SELECT
					DISTINCT v.id,
					v.titulo,
					v.texto,
					v.qtd_escolhas,
					v.data_cadastro,
					v.usuario_cadastro_id,
					ts.id as status_id,
					ts.nome AS status_nome,
					(SELECT
							COUNT(id)
						FROM
							votacoes_opcoes AS vo
						WHERE
							vo.votacao_id = v.id) AS qtde_opcoes,
					FALSE AS permite_votar,
					FALSE AS permite_resultado,
					FALSE AS permite_cancelar,
					FALSE AS permite_alterar,
					FALSE AS permite_ativar,
					FALSE AS permite_finalizar
				FROM
					votacoes AS v
					INNER JOIN
						tipos_status ts ON ts.id = v.status_id
					LEFT JOIN
						votacoes_fiscais vf ON vf.votacao_id = v.id
					LEFT JOIN
						votacoes_grupos vg ON vg.votacao_id = v.id
						AND vg.status_id = 1
					LEFT JOIN
						usuarios_grupos ug ON ug.grupo_id = vg.grupo_id
						AND ug.status_id = 1
				WHERE
					v.status_id != 4
					{$sqlCpl}
					{$sqlCpl2}
				ORDER BY v.titulo , v.id ASC;";
		$query = $this->db->query($sql);
		$result = $query->getResult();

		return $result;
	}
}
