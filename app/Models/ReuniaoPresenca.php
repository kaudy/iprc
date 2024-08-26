<?php

namespace App\Models;

use CodeIgniter\Model;

class ReuniaoPresenca extends Model {
	protected $table            = 'reunioes_presencas';
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
	public function listar($id=null, $reuniao_id=null, $grupo_id=null, $status_id=null, $usuario_id=null) {
		$sqlCpl = "";

		if($id != null) {
			$sqlCpl .= ((trim($sqlCpl) == '') ? ' WHERE ' : ' AND ').(" rp.id={$id} ");
		}
		if($reuniao_id != null) {
			$sqlCpl .= ((trim($sqlCpl) == '') ? ' WHERE ' : ' AND ').(" rp.reuniao_id={$reuniao_id} ");
		}
		if($usuario_id != null) {
			$sqlCpl .= ((trim($sqlCpl) == '') ? ' WHERE ' : ' AND ').(" rp.usuario_id={$usuario_id} ");
		}
		/*if($grupo_id != null) {
			$sqlCpl .= ((trim($sqlCpl) == '') ? ' WHERE ' : ' AND ').(" rg.grupo_id={$grupo_id} ");
		}*/
		if($status_id != null) {
			$sqlCpl .= ((trim($sqlCpl) == '') ? ' WHERE ' : ' AND ').(" rp.status_id={$status_id} ");
		}

		$sql = "SELECT
					rp.*, ts.nome AS status_nome, p.nome AS pessoa_nome
				FROM
					reunioes_presencas rp
				INNER JOIN
					tipos_status ts ON ts.id = rp.status_id
				INNER JOIN
					usuarios u ON u.id = rp.usuario_id
				INNER JOIN
					pessoas p ON p.id = u.pessoa_id
				{$sqlCpl}
				ORDER BY p.nome ASC";
		$query = $this->db->query($sql);
		$result = $query->getResult();
		return $result;
	}

	/**
	 *	Verifica se usuário possui o grupo vinculado a reunião
	 */
	public function verificaPermissaoJustificativaUsuario($reunião_id, $usuario_id) {
		$sql = "SELECT
					grupo_id
				FROM
					usuarios_grupos ug
				WHERE
					ug.usuario_id = {$usuario_id}
					AND status_id = 1
					AND ug.grupo_id IN (
						SELECT
							r.grupo_id
						FROM
							reunioes r
						WHERE
							r.id = {$reunião_id}
						UNION SELECT
							rg.grupo_id
						FROM
							reunioes r
								INNER JOIN
							reunioes_grupos rg ON rg.reuniao_id = r.id
						WHERE
							r.id = {$reunião_id}
					);";
		$query = $this->db->query($sql);
		$result = $query->getResult();

		return $result;
	}

	/**
	 * Lista todos os participantes da reunião com status de presença e justificativas
	 * Caso a presença status esteva vazio é que não foi lançado a presença/justificativa ainda
	 */
	public function listaTodosParticipantes($reuniao_id) {
		$sql = "SELECT DISTINCT
					u.id,
					p.nome AS pessoa_nome,
					IFNULL(rp.status_id, 3) AS presenca_status_id,
					IFNULL(ts.nome, 'pendente') AS presenca_status_nome,
					rp.justificativa
				FROM
					usuarios u
						INNER JOIN
					usuarios_grupos ug ON ug.usuario_id = u.id
						INNER JOIN
					pessoas p ON p.id = u.pessoa_id
						LEFT JOIN
					reunioes_presencas rp ON rp.usuario_id = u.id
						AND rp.reuniao_id = {$reuniao_id}
						LEFT JOIN
					tipos_status ts ON ts.id = rp.status_id
				WHERE
					u.status_id = 1
					AND ug.grupo_id IN (SELECT
							r.grupo_id
						FROM
							reunioes r
						WHERE
							r.id = {$reuniao_id}
						UNION SELECT
							rg.grupo_id
						FROM
							reunioes r
								INNER JOIN
							reunioes_grupos rg ON rg.reuniao_id = r.id
						WHERE
							r.id = {$reuniao_id});";

		$query = $this->db->query($sql);
		$result = $query->getResult();

		return $result;
	}
}
