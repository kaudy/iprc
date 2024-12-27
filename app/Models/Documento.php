<?php

namespace App\Models;

use CodeIgniter\Model;

class Documento extends Model
{
    protected $table            = 'documentos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    //protected $createdField  = 'created_at';
    //protected $updatedField  = 'updated_at';
    //protected $deletedField  = 'deleted_at';

    // Validation
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

	/**
	 * Lista todos os arquivos
	 */
	public function listar($options = array()) {
		$sqlCpl = "";


		if($options != null && count($options) > 0) {
			if(isset($options['id']) && $options['id'] != null) {
				$sqlCpl .= " AND d.id='{$options['id']}' ";
			}
			if(isset($options['status_id']) && $options['status_id'] != null) {
				$sqlCpl .= "AND d.status_id={$options['status_id']} ";
			}
			if(isset($options['nome']) && $options['nome'] != null) {
				$sqlCpl .= "AND d.nome like '%{$options['nome']}%' ";
			}
			if(isset($options['grupo_id']) && $options['grupo_id'] != null) {
				$sqlCpl .= " AND r.grupo_id='{$options['grupo_id']}' ";
			}
		}

		$sql = "SELECT
					d.*, r.titulo AS reuniao_titulo
				FROM
					documentos d
						INNER JOIN
					reunioes r ON r.id = d.referencia_id
						AND d.vinculo = 'reunião'
				WHERE
					d.status_id = 1
					{$sqlCpl}
				ORDER BY d.nome , d.data_cadastro ASC;";
		$query = $this->db->query($sql);
		$result = $query->getResult();
		return $result;
	}
}
