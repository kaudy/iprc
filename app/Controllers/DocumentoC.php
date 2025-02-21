<?php

namespace App\Controllers;

use App\Models\Documento;
use App\Models\DocumentoHistorico;
use CodeIgniter\Files\File;
use Mpdf\Mpdf;
use Symfony\Component\Filesystem\Filesystem;
use Xthiago\PDFVersionConverter\Guesser\RegexGuesser;
use Xthiago\PDFVersionConverter\Converter\GhostscriptConverterCommand;
use Xthiago\PDFVersionConverter\Converter\GhostscriptConverter;

class DocumentoC extends BaseController {

	protected $documento;
	protected $documentoHistorico;

	public function __construct() {
		$this->documento = model(Documento::class);
		$this->documentoHistorico = model(DocumentoHistorico::class);
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

			//echo "<pre>";var_dump($usuario_sessao);exit;

			// histórico de downloads
			/*$dados = (object) array(
				"documento_id" => $documento->id,
				"tipo" => 'download',
				"usuario_id" => $usuario_sessao->usuario->id,
				"usuario_ip" => getIpClient(),
				"data_cadastro" => date('Y-m-d H:i:s')
			);
			$this->documentoHistorico->insert($dados);*/

			//return $this->response->download("./documentos/{$documento->hash}.{$documento->extensao}", null)->setFileName("{$documento->nome}");


			$guesser = new RegexGuesser();
			if($guesser->guess(FCPATH."/documentos/{$documento->hash}.{$documento->extensao}") > 1.4) {
				$command = new GhostscriptConverterCommand();
				$filesystem = new Filesystem();
				$converter = new GhostscriptConverter($command, $filesystem, FCPATH."documentos/tmp");
				$converter->convert(FCPATH."/documentos/{$documento->hash}.{$documento->extensao}", '1.4');
			}

			$this->response->setHeader('Content-Type', 'application/pdf');
			$mpdf = new Mpdf;
			// set the sourcefile
			$mpdf->setSourceFile(FCPATH."/documentos/{$documento->hash}.{$documento->extensao}");

			// import page 1
			$tplIdx = $mpdf->importPage(1);

			//echo "<pre>";var_dump($tplIdx);exit;

			// use the imported page and place it at point 10,10 with a width of 200 mm   (This is the image of the included pdf)
			$mpdf->useTemplate($tplIdx, 10, 10, 200);

			// now write some text above the imported page
			$mpdf->SetTextColor(0, 0, 255);
			$mpdf->SetFont('Arial', 'B', 8);
			$mpdf->SetXY(90, 8);
			$mpdf->Write(0, "Documento confidencial de responsabilitade de: {$usuario_sessao->usuario->nome} IP:$usuario_ip");

			$mpdf->SetWatermarkText("Documento confidencial de responsabilitade de: {$usuario_sessao->usuario->nome}");
			$mpdf->showWatermarkText = true;


			$mpdf->Output();
			//$mpdf->Output('newpdf.pdf');
		}
	}
}