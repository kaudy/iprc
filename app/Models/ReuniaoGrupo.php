<?php

namespace App\Models;

use CodeIgniter\Model;

class ReuniaoGrupo extends Model {
	protected $table            = 'reunioes_grupos';
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
	//protected $deletedField  = 'deleted_at';

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
	 * Lista todos os grupos vinculados a reunião
	 */
	public function listar($id=null, $reuniao_id=null, $grupo_id=null, $status_id=null) {
		$sqlCpl = "";

		if($id != null) {
			$sqlCpl .= ((trim($sqlCpl) == '') ? ' WHERE ' : ' AND ').(" rg.id={$id} ");
		}
		if($reuniao_id != null) {
			$sqlCpl .= ((trim($sqlCpl) == '') ? ' WHERE ' : ' AND ').(" rg.reuniao_id={$reuniao_id} ");
		}
		if($grupo_id != null) {
			$sqlCpl .= ((trim($sqlCpl) == '') ? ' WHERE ' : ' AND ').(" rg.grupo_id={$grupo_id} ");
		}
		if($status_id != null) {
			$sqlCpl .= ((trim($sqlCpl) == '') ? ' WHERE ' : ' AND ').(" rg.status_id={$status_id} ");
		}

		$sql = "SELECT
					rg.id,
					rg.reuniao_id,
					r.titulo AS reuniao_titulo,
					rg.grupo_id,
					g.nome AS grupo_nome,
					rg.status_id
				FROM
					reunioes_grupos AS rg
						INNER JOIN
					reunioes r ON r.id = rg.reuniao_id
						INNER JOIN
					grupos g ON g.id = rg.grupo_id
				{$sqlCpl}
				ORDER BY g.nome ASC;";
		$query = $this->db->query($sql);
		$result = $query->getResult();

		return $result;
	}

	/**
	 *	Verifica se usuário possui o grupo vinculado a reunião
	 */
	public function verificaPermissaoGrupo($votacao_id, $usuario_id) {
		$sql = "SELECT
					vg.id,
					vg.votacao_id,
					vg.grupo_id,
					g.nome AS grupo_nome,
					vg.status_id
				FROM
					votacoes_grupos AS vg
						INNER JOIN
					usuarios_grupos ug ON ug.grupo_id = vg.grupo_id
						INNER JOIN
					grupos g ON g.id = vg.grupo_id
				WHERE
					vg.votacao_id = {$votacao_id}
					AND ug.usuario_id = {$usuario_id}
					AND vg.status_id = 1
					AND ug.status_id = 1;";
		$query = $this->db->query($sql);
		$result = $query->getResult();

		return $result;
	}
}
