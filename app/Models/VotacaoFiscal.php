<?php

namespace App\Models;

use CodeIgniter\Model;

class VotacaoFiscal extends Model {
	protected $table            = 'votacoes_fiscais';
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
	 * Lista todas as votações cadastradas
	 */
	public function listar($id=null, $votacao_id=null, $usuario_id=null, $status=null) {
		$sqlCpl = "";

		if($id != null) {
			$sqlCpl .= ((trim($sqlCpl) == '') ? ' WHERE ' : ' AND ').(" vf.id={$id} ");
		}
		if($votacao_id != null) {
			$sqlCpl .= ((trim($sqlCpl) == '') ? ' WHERE ' : ' AND ').(" vf.votacao_id={$votacao_id} ");
		}
		if($usuario_id != null) {
			$sqlCpl .= ((trim($sqlCpl) == '') ? ' WHERE ' : ' AND ').(" vf.usuario_id={$grupo_id} ");
		}
		if($status != null) {
			$sqlCpl .= ((trim($sqlCpl) == '') ? ' WHERE ' : ' AND ').(" vf.status={$status} ");
		}

		$sql = "SELECT
					vf.id,
					vf.votacao_id,
					vf.usuario_id,
					p.nome,
					vf.status AS status_id,
					ts.nome AS status_nome,
					vf.data_cadastro
				FROM
					votacoes_fiscais vf
						INNER JOIN
					usuarios u ON u.id = vf.usuario_id
						INNER JOIN
					pessoas p ON p.id = u.pessoa_id
						INNER JOIN
					tipos_status ts ON ts.id = vf.status
				{$sqlCpl}
				ORDER BY p.nome ASC;";
		$query = $this->db->query($sql);
		$result = $query->getResult();

		return $result;
	}
}
