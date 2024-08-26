<?php

namespace App\Models;

use CodeIgniter\Model;

class Modulo extends Model
{
	protected $table            = 'modulos';
	protected $primaryKey       = 'id';
	protected $useAutoIncrement = true;
	protected $returnType       = 'object';
	protected $useSoftDeletes   = false;
	protected $protectFields    = true;
	protected $allowedFields    = [];

	// Dates
	protected $useTimestamps = false;
	protected $dateFormat    = 'datetime';
	// protected $createdField  = 'created_at';
	// protected $updatedField  = 'updated_at';
	// protected $deletedField  = 'deleted_at';

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
	 * Lista os mudulos pelo id do perfil
	 */
	public function listarPorPefil($perfil_id) {
		$sql = "SELECT
				m.*,
				null as filhos
			FROM
				modulos m
					INNER JOIN
				modulos_perfis mp ON mp.modulo_id = m.id
			WHERE
				mp.perfil_id = {$perfil_id}
			AND
				m.modulo_pai IS NULL
			AND
				m.status_id = 1;";
		$query = $this->db->query($sql);
		$modulos = $query->getResult();
		// verifica os modulos filhos
		foreach ($modulos as $c => $v) {
			$sql = "SELECT
				m.*
			FROM
				modulos m
			WHERE
				m.modulo_pai = {$v->id}
			AND
				m.status_id = 1;";
			$query = $this->db->query($sql);
			$result = $query->getResult();
			if($result && count($result) >0) {
				$modulos[0]->filhos = (object) $result;
			}
		}


		return $modulos;
	}
}
