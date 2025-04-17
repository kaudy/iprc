<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioGrupo extends Model
{
    protected $table            = 'usuarios_grupos';
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
	 * Lista todos os grupos vinculados com usuário
	 */
	public function listar($usuario_id=null, $grupo_id=null) {
		$sqlCpl = "";
		if($usuario_id != null) {
			$sqlCpl .= " AND u.id={$usuario_id} ";
		}
		if($grupo_id != null) {
			$sqlCpl .= " AND g.id={$grupo_id} ";
		}

		$sql = "SELECT
					ug.*,
					g.nome AS 'grupo_nome',
					u.pessoa_id
				FROM
					usuarios_grupos AS ug
						INNER JOIN
					grupos g ON g.id = ug.grupo_id
						INNER JOIN
					usuarios u ON u.id = ug.usuario_id
				WHERE
					ug.status_id = 1
					{$sqlCpl}
				ORDER BY g.nome ASC;";
		$query = $this->db->query($sql);
		$result = $query->getResult();

		return $result;
	}
}
