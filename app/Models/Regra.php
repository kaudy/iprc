<?php

namespace App\Models;

use CodeIgniter\Model;

class Regra extends Model
{
    protected $table            = 'regras';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    //protected $createdField  = 'created_at';
    //protected $updatedField  = 'updated_at';
    //protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

	/**
	 * Verifica se o usuário possui um perfil com a regra informada
	 */
	public function possuiRegra($usuario_id, $regra_id) {
		$sqlCpl = "";

		$sql =" SELECT
					r.id AS regra_id, p.id AS perfil_id, u.id AS usuario_id
				FROM
					regras r
						INNER JOIN
					regras_perfis rp ON rp.regra_id = r.id
						INNER JOIN
					perfis p ON p.id = rp.perfil_id
						INNER JOIN
					usuarios u ON u.perfil_id = p.id
				WHERE
					r.id = {$regra_id} AND u.id = {$usuario_id};";
		$query = $this->db->query($sql);
		$result = $query->getResult();

		return count($result) > 0 ? true : false;
	}
}
