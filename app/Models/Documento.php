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
				$sqlCpl .= " AND g.id='{$options['grupo_id']}' ";
			}
			if(isset($options['vinculo']) && $options['vinculo'] != null) {
				$sqlCpl .= " AND d.vinculo='{$options['vinculo']}' ";
			}
			if(isset($options['tipo']) && $options['tipo'] != null) {
				$sqlCpl .= " AND d.tipo='{$options['tipo']}' ";
			}
		}

		$sql = "SELECT
					d.*,
					r.titulo AS reuniao_titulo,
					g1.id as grupo_reuniao_id,
					g1.nome as grupo_reuniao_nome,
					g.nome as grupo_nome,
					FALSE AS permite_excluir,
					FALSE AS permite_alterar,
					FALSE AS permite_download
				FROM
					documentos d
				LEFT JOIN
					reunioes r ON r.id = d.referencia_id and d.vinculo = 'reunião'
				LEFT JOIN
					grupos g1 ON g1.id = r.grupo_id
				LEFT JOIN
					grupos g ON g.id = d.referencia_id and d.vinculo = 'grupo'
				WHERE
					d.status_id = 1
					{$sqlCpl}
				ORDER BY d.nome , d.data_cadastro ASC;";
		$query = $this->db->query($sql);
		$result = $query->getResult();

		// Valida permissões arquivos
		if(isset($options['valida_propriedade']) && $options['valida_propriedade'] == true) {
			if(isset($options['proprietario_id']) && $options['proprietario_id'] != null) {
				foreach($result as $c => $v) {
					if($v->vinculo == 'reunião' && $v->permissao == 'grupo') {
						$sql = "SELECT
									ug.*
								FROM
									usuarios_grupos ug
								WHERE
									ug.usuario_id = {$options['proprietario_id']}
									AND ug.grupo_id = {$v->grupo_reuniao_id}
									AND ug.status_id = 1;";
						$query = $this->db->query($sql);
						$result_permissao = $query->getResult();

						// Remove se nenhuma permissão encontrada
						if(count($result_permissao) == 0) {
							unset($result[$c]);
						}
					}elseif($v->vinculo == 'reunião' && $v->permissao == 'proprietário') {
						// Remove se nenhuma permissão encontrada
						if($v->usuario_cadastro_id != $options['proprietario_id']) {
							unset($result[$c]);
						}
					}
				}
			}
		}

		return $result;
	}
}
