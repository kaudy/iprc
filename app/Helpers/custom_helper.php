<?php
	use CodeIgniter\CodeIgniter;
	use PHPMailer\PHPMailer\PHPMailer;

	/**
	 * Busca o ip externo do cliente
	 */
	function getIpClient() {
		$ip = file_get_contents('https://api64.ipify.org');
		return $ip;
	}

	/**
	 * Busca valores registrados no enum de um campo
	*/
	function getEnum($table, $field) {
		$db = db_connect();
		$sql = "SHOW COLUMNS FROM $table LIKE '$field'";
		$query = $db->query($sql);
		$result = $query->getRow();
		$dados = explode ("enum", $result->Type);

		if(isset($dados[1])) {
			$a = preg_replace("[(\(|\')]", "", $dados[1]);
			$a = preg_replace("[\)]", "", $a);
			$dados = explode(",",$a);
		}
		return $dados;
	}

	/**
	* Converte de yyyy-mm-dd para dd/mm/aaaa
	*/
	function DataConvertBr($data) {
		$d = explode('-', $data);
		return "{$d[2]}/{$d[1]}/{$d[0]}";
	}

	/**
	* Converte de yyyy-mm-dd 00:00:00 para dd/mm/aaaa 00:00
	*/
	function DataHoraConvertBrString($data) {
		$aux = explode(' ', $data);
		$d = explode('-', $aux[0]);
		$h = explode(':', $aux[1]);
		return "{$d[2]}/{$d[1]}/{$d[0]} às {$h[0]}:{$h[1]}h";
	}

	/**
	* Converte de yyyy-mm-dd 00:00:00 para dd/mm/aaaa
	*/
	function DataConvertBrString($data) {
		$aux = explode(' ', $data);
		$d = explode('-', $aux[0]);
		return "{$d[2]}/{$d[1]}/{$d[0]}";
	}

	/**
	* Converte de yyyy-mm-dd 00:00:00 para 00:00
	*/
	function HoraConvertBrString($data) {
		$aux = explode(' ', $data);
		$h = explode(':', $aux[1]);
		return "{$h[0]}:{$h[1]}";
	}

	/**
	* Converte de dd/mm/yyyy  para  yyyy-mm-dd
	*/
	function DataConvertDB($data) {
		$d = explode('/', $data);
		return "{$d[2]}-{$d[1]}-{$d[0]}";
	}

	/**
	* Formata CEP
	*/
	function mascaraCep($string) {
		if (!empty ($string) && strlen($string) > 0) {
			while (strlen($string) < 8) {
				$string = '0' . $string;
			}
			return substr($string, 0, 2) . '.' . substr($string, 2, 3) . '-' . substr($string, 5);
		} else {
			return '';
		}
	}

	/**
	* Formata CPF
	*/
	function mascaraCpf($string) {
		if (!empty($string)) {
			$string = somenteNumeros($string);
			$string = str_pad($string, 11, "0", STR_PAD_LEFT);
			return substr($string, 0, 3).'.'.substr($string, 3, 3).'.'.substr($string, 6, 3).'-'.substr($string, 9);
		}
		return $string;
	}

	/**
	* Somente números
	*/
	function somenteNumeros($string) {
		return preg_replace('/[^0-9]/', '', $string);
	}

	/*
	 * Retorna o link encurtado.
	 */
	function linkEncurtado($link) {
		$curl = curl_init('http://tinyurl.com/api-create.php?url='.$link);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);
		curl_setopt($curl, CURLOPT_USERAGENT, getenv('HTTP_USER_AGENT'));
		return curl_exec($curl);
	}

	/**
	 * Primeiro nome
	 */
	function primeiroNome($nome_completo) {
		$parts = explode(" ", $nome_completo);
		return ucfirst($parts[0]);
	}

	/**
	 *	Retorna o nome do status solicitado pelo id
	 */
	function statusNome($tipo_status_id) {
		$db = db_connect();
		$sql = "SELECT
					ts.nome
				FROM
					tipos_status ts
				WHERE
					ts.id = {$tipo_status_id};";
		$query = $db->query($sql);
		$result = $query->getRow();
		if($result->nome != '' && $result->nome != null) {
			return ucfirst($result->nome);
		}else {
			return ucfirst("Não Identificado(ERR-01)");
		}
	}

	/**
	 *	Verifica o texto e abrevia o texto para quantidade
	 *  de caracteres informado
	 */
	function textoEncurtado($texto, $max_caracteres=50) {
		if(strlen($texto) > $max_caracteres) {
			$texto_reduzido = substr($texto, 0, 50);
			return "{$texto_reduzido}...";
		}else {
			return $texto;
		}
	}

	/**
	 *	Retorna a configuração do sistema
	 */
	function sistemaCFG($sistema_cfg_id) {
		$db = db_connect();
		$sql = "SELECT
					sc.*
				FROM
					sistema_cfg sc
				WHERE
					sc.id = {$sistema_cfg_id}
				AND status_id = 1;";
		$query = $db->query($sql);
		$result = $query->getRow();

		switch (getenv('CI_ENVIRONMENT')) {
			case 'development':
				return $result->desenvolvimento;
			case 'test':
				return $result->homologacao;
			case 'production':
				return $result->producao;
			default:
				return $result->desenvolvimento;
		}
	}

	/**
	 * Envia email pelo PHPMailer
	 */
	function enviaEmail($dados_email) {
		if($dados_email) {
			// Configuações
			$mail = new PHPMailer;
			$mail->isSMTP();
			$mail->SMTPDebug = 0; // 2-DEBUG TOTAL
			$mail->setLanguage('br');
			$mail->CharSet = getenv('email.CharSet') ? getenv('email.CharSet') : 'UTF-8';
			$mail->Host = getenv('email.host') ? getenv('email.host') : 'sandbox.smtp.mailtrap.io';
			$mail->Port = getenv('email.port') ? getenv('email.port') : 25;
			$mail->SMTPAuth = getenv('email.smtpauth') ? getenv('email.smtpauth') : true;
			$mail->SMTPAutoTLS = getenv('email.smtpautotls') ? getenv('email.smtpautotls') : false;
			$mail->SMTPSecure = getenv('email.SMTPSecure') ? getenv('email.SMTPSecure') : 'tls';
			$mail->Username = getenv('email.username') ? getenv('email.username') : 'f28874ad168bc9';
			$mail->Password = getenv('email.Password') ? getenv('email.Password') : '0aa6ba0dd16f30';
			$mail->setFrom((getenv('email.fromemail') ? getenv('email.fromemail') : 'iprc@paranaclube.com.br'), (getenv('email.fromdescription') ? getenv('email.fromdescription') : 'iPRC'));

			// Monta os dados do email
			$mail->addAddress($dados_email->email_destinatario, $dados_email->nome_destinatario);
			$mail->Subject = "iPRC - {$dados_email->titulo}";
			$mail->Body = isset($dados_email->corpo) ? $dados_email->corpo : null;
			//$mail->addReplyTo('test@hostinger-tutorials.com', 'Your Name');
			//$mail->addCC('cc@exemplo.com');
			//$mail->addBCC('bcc@exemplo.com');

			if(isset($dados_email->template) && $dados_email->template != null) {
				$mail->Body = null;
				$mail->isHTML(true); //Define formato do email em HTML
				$mail->msgHTML($dados_email->template);
				$mail->AltBody = 'Para ver a mensagem, por favor utilize um gerenciador de email compativel com HTML!';
			}else {
				$mail->Body = $dados_email->corpo;
			}

			//echo '<pre>'; var_dump($mail);exit;

			if(!$mail->send()) {
				echo 'Mailer Error: ' . $mail->ErrorInfo;
				return (object) ['status' => false, 'erro' => $mail->ErrorInfo];
			}else {
				echo 'The email message was sent.';
				return (object) ['status' => true, 'erro' => null];
			}
		}else {
			return (object) ['status' => false, 'erro' => 'Dados do email inválidos.'];
		}
	}


