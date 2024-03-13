<?php

namespace App\Models;

use CodeIgniter\Model;

class VotacaoOpcao extends Model {
    protected $table            = 'votacoes_opcoes';
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
	 *	Retorna a quantidade de votos das opções conforme a votação informada
	 */
	public function listarResultadoOpcoes($votacao_id) {
		$sql = "SELECT
					vo.id,
					vo.titulo,
					vo.descricao,
					(SELECT
							COUNT(v1.id)
						FROM
							votos v1
						WHERE
							v1.votacao_id = {$votacao_id} AND v1.voto = vo.id) AS votos
				FROM
					votacoes_opcoes vo
				WHERE
					vo.votacao_id = {$votacao_id};";
		$query = $this->db->query($sql);
		$result = $query->getResult();

		return $result;
	}
}
