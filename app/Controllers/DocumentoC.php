<?php

namespace App\Controllers;

use App\Models\Documento;
use App\Models\DocumentoHistorico;
use App\Models\Usuario;
use App\Models\Pessoa;
use CodeIgniter\Files\File;
use Mpdf\Mpdf;
use Symfony\Component\Filesystem\Filesystem;
use Xthiago\PDFVersionConverter\Guesser\RegexGuesser;
use Xthiago\PDFVersionConverter\Converter\GhostscriptConverterCommand;
use Xthiago\PDFVersionConverter\Converter\GhostscriptConverter;

class DocumentoC extends BaseController {

	protected $documento;
	protected $documentoHistorico;
	protected $usuario;
	protected $pessoa;

	public function __construct() {
		$this->documento = model(Documento::class);
		$this->documentoHistorico = model(DocumentoHistorico::class);
		$this->usuario = model(Usuario::class);
		$this->pessoa = model(Pessoa::class);
	}

	public function index() {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao)) {
			return redirect()->route('login');
		}
		// mensagem temporaria da sessao
		$data = $this->session->getFlashdata('data');

		$documentos = array();
		if(!isset($data)) {
			$data['msg'] = "";
			$data['msg_type'] = "";
			$data['errors'] = [];
		}

		if($this->request->getMethod() === 'post') {
			//echo "<pre>";var_dump($this->request->getPost());exit;
			$nome = $this->request->getPost('nome');
			$documentos = $this->documento->listar(array("nome" => $nome));
		}

		// TODO: Criar regra para permissões de cadastro de documentos
		$permite_cadastrar_documento = true;

		//Permissões
		$this->smarty->assign("permite_cadastrar_documento", $permite_cadastrar_documento);
		// Dados
		$this->smarty->assign("documentos", $documentos);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/documento/listar.tpl');
	}


	/**
	 * Upload de arquivos
	 */
	public function upload() {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao)) {
			return redirect()->route('login');
		}
		// mensagem temporaria da sessao
		$data = $this->session->getFlashdata('data');
		if(!isset($data)) {
			$data['msg'] = "";
			$data['msg_type'] = "";
			$data['errors'] = [];
		}
	}

	/**
	 * Gerencia o download de um arquivo
	 */
	public function download($arquivo_hash) {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao)) {
			return redirect()->route('login');
		}
		// mensagem temporaria da sessao
		$data = $this->session->getFlashdata('data');
		if(!isset($data)) {
			$data['msg'] = "";
			$data['msg_type'] = "";
			$data['errors'] = [];
		}
		$documento = $this->documento->where('hash', $arquivo_hash)->first();

		if(!$documento) {
			return redirect()->route('/');
		}else {
			$usuario_ip = getIpClient();
			$usuario = $this->usuario->where('id', $usuario_sessao->usuario->id)->first();
			$pessoa = $this->pessoa->where('id', $usuario->pessoa_id)->first();
			if($usuario->status_id != 1) {
				return redirect()->route('/');
			}

			/**
			 * TODO:
			 * - Validar qual tipo de arquivo está sendo feito download
			 * - Se o arquivo for PDF validar se ghostscript esta instalado
			 * - Se o arquivo for PDF converter para PDF 1.4
			 * - Se for pdf coloca marca dagua em todas as paginas do pdf e informações de quem fez o download
			 * - Salvar histórico de downloads
			 */

			//echo "<pre>";var_dump($usuario_sessao);exit;

			// histórico de downloads
			$dados = (object) array(
				"documento_id" => $documento->id,
				"tipo" => 'download',
				"usuario_id" => $usuario_sessao->usuario->id,
				"usuario_ip" => getIpClient(),
				"data_cadastro" => date('Y-m-d H:i:s')
			);
			$this->documentoHistorico->insert($dados);

			// Valida qual tipo do arquivo está sendo feito download
			if($documento->extensao != 'pdf') {
				return $this->response->download("./documentos/{$documento->hash}.{$documento->extensao}", null)->setFileName("{$documento->nome}");

			}else {
				$guesser = new RegexGuesser();

				// Verifica se precisa converter para PDF 1.4
				if($guesser->guess(FCPATH."/documentos/{$documento->hash}.{$documento->extensao}") > 1.4) {
					// Se o arquivo for PDF validar se ghostscript esta instalado
					system("which gs > /dev/null", $gs_instado);
					$gs_version = shell_exec("gs --version");

					// Se Ghostscript estiver instalado, converter para PDF 1.4
					if($gs_instado == 0 && $gs_version > 8.0) {
						$command = new GhostscriptConverterCommand();
						$filesystem = new Filesystem();
						$converter = new GhostscriptConverter($command, $filesystem, FCPATH."documentos/tmp");
						$converter->convert(FCPATH."/documentos/{$documento->hash}.{$documento->extensao}", '1.4');
					}else {
						$data['msg'] = "O arquivo precisa ser convertido para PDF 1.4 para ser baixado.";
						$data['msg_type'] = "danger";
						return redirect()->route('documento')->with('data', $data);
					}
				}

				$this->response->setHeader('Content-Type', 'application/pdf');
				$mpdf = new Mpdf;
				// set the sourcefile
				$total_paginas = $mpdf->setSourceFile(FCPATH."/documentos/{$documento->hash}.{$documento->extensao}");


				for($i = 1; $i <= $total_paginas; $i++) {
					// import page 1
					$tplIdx = $mpdf->importPage($i);
					$mpdf->SetDisplayMode('fullpage');

					//$mpdf->useTemplate($tplIdx, 0, 0, 200);
					$mpdf->useTemplate($tplIdx);

					// Marca dagua
					$mpdf->SetWatermarkText("Documento confidencial - {$pessoa->documento} - IP: {$usuario_ip}");
					$mpdf->showWatermarkText = true;
					$mpdf->SetWatermarkText(new \Mpdf\WatermarkText("Documento confidencial", 100, 45, 'red', 0.4));
					$mpdf->showWatermarkText = true;
					// Cabeçalho
					$cabecalho_text = '	<div style="text-align: center; text-height: 16px; color: red">
											Documento confidencial de responsabilidade de: '.$usuario_sessao->usuario->nome.'
											de CPF/CNPJ '.$pessoa->documento.'. Download realizado pelo IP: '.$usuario_ip.'
										</div>';
					$mpdf->SetTextColor(0, 0, 255);
					$mpdf->SetFont('Arial', 'B', 10);
					$mpdf->SetXY(60, 10);
					$mpdf->WriteHTML($cabecalho_text);
					// Rodapé
					$rodape_text = '	<div style="text-align: center; text-height: 16px; color: red">
											Documento confidencial de responsabilidade de: '.$usuario_sessao->usuario->nome.'
											de CPF/CNPJ '.$pessoa->documento.'. Download realizado pelo IP: '.$usuario_ip.'
										</div>';
					$mpdf->SetTextColor(0, 0, 255);
					$mpdf->SetFont('Arial', 'B', 10);
					$mpdf->SetXY(60, 270);
					$mpdf->WriteHTML($rodape_text);
					// meio do texto
					$mpdf->SetTextColor(255, 0, 255);
					$mpdf->SetAlpha(0.5);
					$mpdf->SetXY(10, 100);
					$html = '<div style="position: absolute; rotate: -90; text-rotate=45; text-align: center; color: red">
								Documento confidencial de responsabilitade de: '.$usuario_sessao->usuario->nome.'<br>
								de CPF/CNPJ '.$pessoa->documento.' e download pelo IP: '.$usuario_ip.'
							</div>';
					$mpdf->WriteHTML($html);

					if($i <= $total_paginas) {
            			$mpdf->AddPage();
   					}
				}

				$mpdf->Output();
			}
		}
	}
}