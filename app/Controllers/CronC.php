<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Reuniao;
use App\Models\Usuario;
use App\Models\Regra;
use App\Models\TipoStatus;
use App\Models\Grupo;
use App\Models\ReuniaoGrupo;
use App\Models\ReuniaoPresenca;
use App\Models\Documento;
use App\Models\UsuarioGrupo;
use App\Models\Pessoa;
use App\Models\EnvioEmail;

class CronC extends BaseController {

	public function __construct() {
	}

	/**
	 *	Envia emails agendados na tabela envio_emails
	 */
	public function enviarEmails() {
		$envioEmail = model(EnvioEmail::class);

		$tempo = 120; // 2 minutos
		$emails_enviar = $envioEmail->where('status_id', 3)->find();

		if(count($emails_enviar) > 0) {
			foreach($emails_enviar as $email_dados) {
				$payload = json_decode($email_dados->payload);
				$this->smarty->assign("payload", $payload);
				$template = $this->smarty->fetch($this->smarty->getTemplateDir(0).$email_dados->template);

				// Monta os dados do email
				$dados = (object) array(
					'email_destinatario'=> $email_dados->destinatario,
					'nome_destinatario' => $payload->nome,
					'titulo'			=> $email_dados->titulo,
					'template'			=> $template
				);

				// Envia email
				$retorno = enviaEmail($dados);
				if($retorno) {
					$envioEmail->update($email_dados->id, (object) ['status_id' => 11]);
				}else {
					$envioEmail->update($email_dados->id, (object) ['tentativa_reenvio' => $email_dados->tentativa_reenvio + 1]);
				}

				// Aguarda tempo para enviar o proximo email
				sleep($tempo);
			}
		}
	}
}