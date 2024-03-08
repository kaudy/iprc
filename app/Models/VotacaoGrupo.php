<?php

namespace App\Models;

use CodeIgniter\Model;

class VotacaoGrupo extends Model
{
    protected $table            = 'votacoes_grupos';
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
	public function listar($id=null, $votacao_id=null, $grupo_id=null, $status=null) {
		$sqlCpl = "";

		if($id != null) {
			$sqlCpl .= ((trim($sqlCpl) == '') ? ' WHERE ' : ' AND ').(" vg.id={$id} ");
		}
		if($votacao_id != null) {
			$sqlCpl .= ((trim($sqlCpl) == '') ? ' WHERE ' : ' AND ').(" vg.votacao_id={$votacao_id} ");
		}
		if($grupo_id != null) {
			$sqlCpl .= ((trim($sqlCpl) == '') ? ' WHERE ' : ' AND ').(" vg.grupo_id={$grupo_id} ");
		}
		if($status != null) {
			$sqlCpl .= ((trim($sqlCpl) == '') ? ' WHERE ' : ' AND ').(" vg.status={$status} ");
		}

		$sql = "SELECT
					vg.id,
					vg.votacao_id,
					v.titulo AS votacao_titulo,
					vg.grupo_id,
					g.nome AS grupo_nome,
					vg.status
				FROM
					votacoes_grupos AS vg
						INNER JOIN
					votacoes v ON v.id = vg.votacao_id
						INNER JOIN
					grupos g ON g.id = vg.grupo_id
				{$sqlCpl}
				ORDER BY g.nome ASC;";
		$query = $this->db->query($sql);
		$result = $query->getResult();

		return $result;
	}

	/**
	 *	Verifica se usuário possui o grupo vinculado a votação
	 */
	public function verificaPermissaoGrupo($votacao_id, $usuario_id) {
		$sql = "SELECT
					vg.id,
					vg.votacao_id,
					vg.grupo_id,
					g.nome AS grupo_nome,
					vg.status
				FROM
					votacoes_grupos AS vg
						INNER JOIN
					usuarios_grupos ug ON ug.grupo_id = vg.grupo_id
						INNER JOIN
					grupos g ON g.id = vg.grupo_id
				WHERE
					vg.votacao_id = {$votacao_id}
					AND ug.usuario_id = {$usuario_id}
					AND vg.status = 1
					AND ug.status = 1;";
		$query = $this->db->query($sql);
		$result = $query->getResult();

		return $result;
	}
}
