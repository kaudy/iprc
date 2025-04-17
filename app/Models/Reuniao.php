<?php

namespace App\Models;

use CodeIgniter\Model;

class Reuniao extends Model
{
    protected $table            = 'reunioes';
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

    // Validation
    // protected $validationRules      = [];
    // protected $validationMessages   = [];
    // protected $skipValidation       = false;
    // protected $cleanValidationRules = true;

    // Callbacks
    // protected $allowCallbacks = true;
    // protected $beforeInsert   = [];
    // protected $afterInsert    = [];
    // protected $beforeUpdate   = [];
    // protected $afterUpdate    = [];
    // protected $beforeFind     = [];
    // protected $afterFind      = [];
    // protected $beforeDelete   = [];
    // protected $afterDelete    = [];


	/**
	 * Lista todas as votações cadastradas
	 */
	public function listar($id=null, $titulo = null, $status_id = null, $grupo_id = null, $filtros = null) {
		$sqlCpl = "";
		$sqlCpl2 = "";

		if($id != null) {
			$sqlCpl .= " AND r.id='{$id}' ";
		}
		if($status_id != null) {
			$sqlCpl .= "AND r.status_id={$status_id} ";
		}
		if($titulo != null) {
			$sqlCpl .= "AND r.titulo like '%{$titulo}%' ";
		}
		if($grupo_id != null) {
			$sqlCpl .= " AND r.grupo_id='{$grupo_id}' ";
		}
		if($filtros != null) {
			if(isset($filtros["data_reuniao_inicial"]) && $filtros["data_reuniao_inicial"] != null ) {
				$sqlCpl .= " AND r.data_reuniao >='{$filtros["data_reuniao_inicial"]}' ";
			}
			if(isset($filtros["data_reuniao_inicial"]) && $filtros["data_reuniao_final"] != null) {
				$sqlCpl .= " AND r.data_reuniao <='{$filtros["data_reuniao_final"]}' ";
			}
			if(isset($filtros["status_envio_email"]) &&  $filtros["status_envio_email"] != null) {
				$sqlCpl .= " AND r.status_envio_email='{$filtros["status_envio_email"]}' ";
			}
		}

		$sql = "SELECT
					r.*,
					g.nome as grupo_nome,
					ts.id as status_id,
					ts.nome AS status_nome,
					FALSE AS permite_cancelar,
					FALSE AS permite_alterar,
					FALSE AS permite_ativar,
					FALSE AS permite_finalizar,
					FALSE AS permite_justificar
				FROM
					reunioes r
				INNER JOIN
					grupos g ON g.id = r.grupo_id
				INNER JOIN
					tipos_status ts ON ts.id = r.status_id
				{$sqlCpl}
				ORDER BY r.data_reuniao ASC;";
		$query = $this->db->query($sql);
		$result = $query->getResult();
		return $result;
	}

	/**
	 * Adiciona nova reunião a base de dados
	 */
	public function adicionar($dados) {
		try {
			$this->db->transException(true)->transStart();
			$dados_reuniao = array(
				"titulo" => $dados->titulo,
				"descricao" => $dados->descricao,
				"grupo_id" => $dados->grupo_id,
				"status_id" => 3, // pendente
				"data_reuniao" => $dados->data_reuniao,
				"data_cadastro" => $dados->data_cadastro,
				"usuario_cadastro_id" => $dados->usuario_cadastro_id
			);
			$nova_reuniao_id = $this->insert($dados_reuniao);
			if($nova_reuniao_id) {
				$this->db->transComplete();
				return $nova_reuniao_id;
			}else {
				$this->db->transRollback();
				return false;
			}
		} catch (DatabaseException $e) {
			$this->db->transRollback();
			return $e;
		}
	}
}
