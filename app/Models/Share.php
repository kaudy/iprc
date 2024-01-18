<?php

namespace App\Models;
use CodeIgniter\Model;

class Share extends Model{


	/**
	 * Carrega os dados para se
	 */
	public function getHead() {
		


		$payload = array(
			"titulo" => "iPRC",
			"subtitulo" => "Sistema Integrado",
			"logado" => false,
			"usuario" => array(),
			"perfis" => array(),
			"funcionalidades" => array(
				"usuario" => array(
					"id" => 1
				)
			)
		);
		//$head_html = $this->smarty->fetch("{$this->smarty->getTemplateDir(0)}head.tpl");
		//echo "<pre>";var_dump($head_html);exit;
		return $payload;
	}
}
