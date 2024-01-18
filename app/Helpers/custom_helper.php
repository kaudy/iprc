<?php
	use CodeIgniter\CodeIgniter;

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
	* Converte de mm/dd/aaaa para dd/mm/aaaa
	*/
	function DataConvertBr($data) {
		$d = explode('/', $data);
		return "{$d[1]}/{$d[0]}/{$d[2]}";
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
			$string = $this->somenteNumeros($string);
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


