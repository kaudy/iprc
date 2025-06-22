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

		//$tempo = 120; // 2 minutos
		$emails_enviar = $envioEmail->where('status_id', 3)->limit(1)->find();

		//echo '<pre>'; var_dump(count($emails_enviar));exit;

		if(count($emails_enviar) > 0) {

			echo("<pre>".date('Y-m-d H:i:s')." - Cron Enviando email:<br>");
			foreach($emails_enviar as $email_dados) {
				//echo(date('Y-m-d H:i:s')." - Enviando email para ".$email_dados->destinatario."<br>");

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
				echo("Enviando para: {$email_dados->destinatario}<br>");

				// Envia email
				$retorno = enviaEmail($dados);
				if($retorno->status == true) {
					$envioEmail->update($email_dados->id, (object) ['status_id' => 11]);
					//break;
				}else {
					$envioEmail->update($email_dados->id, (object) ['tentativa_reenvio' => $email_dados->tentativa_reenvio + 1]);
					if($email_dados->tentativa_reenvio >= 3) {
						$envioEmail->update($email_dados->id, (object) ['status_id' => 6, 'erro' => $retorno->erro]); // Cancelado
						//echo('<br>'.date('Y-m-d H:i:s')." - Email cancelado para ".$email_dados->destinatario."<br>");

						// envia email informando que o email não foi enviado
						$corpo = "O email não foi enviado para {$email_dados->destinatario} \n";
						$corpo .= "Tentativas: {$email_dados->tentativa_reenvio} \n";
						$corpo .= "Status: {$retorno->status} \n";
						$corpo .= "Motivo: {$retorno->erro} \n";
						$dados_erro = (object) array(
							'email_destinatario'=> 'ti@paranaclube.com.br',
							'nome_destinatario' => 'TI - Parana Clube',
							'titulo'			=> "Erro no envio de email #{$email_dados->id}",
							'corpo'				=> $corpo
						);
						enviaEmail($dados_erro);
					}
					//break;
				}

				// Aguarda tempo para enviar o proximo email
				//sleep($tempo);
			}
			echo('<br>'.date('Y-m-d H:i:s')." Fim de envio de email <br>");
		}
	}
}