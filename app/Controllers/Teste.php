<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Teste extends BaseController
{
	public function index() {
		$data['titulo'] = 'Smarty funcionando!!';
		$data['subtitulo'] = 'mão a obra!!';

		// chamada manual do smarty 
		$this->smarty->assign($data);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'teste.tpl');

		// chamada pelo metodo
		//$this->smartyView('teste', $data);
	}
}
