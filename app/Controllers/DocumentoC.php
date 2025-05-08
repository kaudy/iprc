<?php

namespace App\Controllers;

use App\Models\Documento;
use App\Models\DocumentoHistorico;
use App\Models\Usuario;
use App\Models\Pessoa;
use App\Models\Grupo;
use App\Models\Regra;
use App\Models\Reuniao;
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
	protected $grupo;
	protected $regra;
	protected $reuniao;

	public function __construct() {
		$this->documento = model(Documento::class);
		$this->documentoHistorico = model(DocumentoHistorico::class);
		$this->usuario = model(Usuario::class);
		$this->pessoa = model(Pessoa::class);
		$this->grupo = model(Grupo::class);
		$this->regra = model(Regra::class);
		$this->reuniao = model(Reuniao::class);
	}

	/**
	 * Listar os documentos
	 */
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
			$nome = $this->request->getPost('nome');
			$tipo_documento = $this->request->getPost('tipo_documento');
			$grupo_id = $this->request->getPost('grupo_id');
			$documentos = $this->documento->listar(
				array(
					"nome" => $nome,
					"tipo" => $tipo_documento,
					"grupo_id" => $grupo_id,
					"valida_propriedade" => true,
					"proprietario_id" => $usuario_sessao->usuario->id
				)
			);
			// Verifica permissões das reuniões
			foreach($documentos as $c => $reuniao) {
				// Permite Excluir Arquivo
				if($this->regra->possuiRegra($usuario_sessao->usuario->id, 17)) {
					$documentos[$c]->permite_excluir = true;
				}
				// Permite Alterar documento
				if(($this->regra->possuiRegra($usuario_sessao->usuario->id, 18))) {
					$documentos[$c]->permite_alterar = true;
				}
				// Permite Baixar documento
				if(($this->regra->possuiRegra($usuario_sessao->usuario->id, 19))) {
					$documentos[$c]->permite_download = true;
				}
			}
		}

		// Carrega todos os grupos ativos
		$grupos = $this->grupo->where('status_id', 1)->findAll();

		// permissões de cadastro de documentos
		$permite_cadastrar_documento = $this->regra->possuiRegra($usuario_sessao->usuario->id, 16);

		//Permissões
		$this->smarty->assign("permite_cadastrar_documento", $permite_cadastrar_documento);
		// Dados
		$this->smarty->assign("tipos_documento", getEnum('documentos', 'tipo'));
		$this->smarty->assign("grupos", $grupos);
		$this->smarty->assign("documentos", $documentos);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/documento/listar.tpl');
	}

	/**
	 * Cadastro da votação e grupo que poderao votar
	 */
	public function cadastrarDocumento() {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao) || !$this->regra->possuiRegra($usuario_sessao->usuario->id, 16)) {
			return redirect()->route('login');
		}
		// mensagem temporaria da sessao
		$data = $this->session->getFlashdata('data');
		if(!isset($data)) {
			$data['msg'] = "";
			$data['msg_type'] = "";
			$data['errors'] = [];
		}

		if(count($this->request->getFiles()) > 0) {
			$titulo = $this->request->getPost('titulo');
			$tipo_vinculo = $this->request->getPost('tipo_vinculo');
			$grupo_id = $this->request->getPost('grupo_id');
			$reuniao_id = $this->request->getPost('reuniao_id');
			$tipo_documento = $this->request->getPost('tipo_documento');
			$tipo_permissao = $this->request->getPost('tipo_permissao');
			$marca_dagua = $this->request->getPost('marca_dagua') == "sim" ? 1 : 0;
			$referencia_id = $tipo_vinculo == 'reunião' ? $reuniao_id : $grupo_id;

			$validationRule = [
				'userfile' => [
					'rules' => [
						'uploaded[userfile]',
						'mime_in[userfile,image/jpg,image/jpeg,image/gif,image/png,image/webp,application/pdf]',
						'max_size[userfile,102400]',
					],
				],
			];
			if (! $this->validateData([], $validationRule)) {
				$data = ['errors' => $this->validator->getErrors()];

				$data['msg'] = "Ocorreu um problema ao tentar adicionar o documento.";
				$data['msg_type'] = "danger";
				return redirect()->route('documento')->with('data', $data);
			}
			$file = $this->request->getFile('userfile');
			$nome_arquivo = $file->getName();
			$ext = $file->getClientExtension();
			$timestamp = time();
			$arquivo_hash = hash('sha256', "grupo_{$grupo_id}_{$timestamp}");
			$newName = "{$arquivo_hash}.{$ext}";
			$file->move( './documentos/', $newName);

			// Persistir o documento e caminho no banco de dados
			$dados = (object) array(
				"nome" => $titulo,
				"nome_arquivo" => $nome_arquivo,
				"tipo" => $tipo_documento,
				"vinculo" => $tipo_vinculo,
				"referencia_id" => $referencia_id,
				"status_id" => 1,
				"arquivo" => $newName,
				"hash" => $arquivo_hash,
				"extensao" => $ext,
				"marca_dagua" => $marca_dagua,
				"permissao" => $tipo_permissao,
				"data_cadastro" => date('Y-m-d H:i:s'),
				"usuario_cadastro_id" => $usuario_sessao->usuario->id
			);
			$novo_documento = $this->documento->insert($dados);
			if($novo_documento) {
				$data['msg'] = "Documento adicionado com sucesso";
				$data['msg_type'] = "primary";
				return redirect()->route('documento')->with('data', $data);
			} else {
				$data['msg'] = "Ocorreu um problema ao tentar adicionar o documento. Erros encontrados:";
				$data['msg_type'] = "danger";
				array_push($data['errors'], $novo_documento);
				$status = false;
			}
		}

		// Carrega todos os grupos ativos
		$grupos = $this->grupo->where('status_id', 1)->findAll();

		$this->smarty->assign("tipos_documento", getEnum('documentos', 'tipo'));
		$this->smarty->assign("tipos_vinculos", getEnum('documentos', 'vinculo'));
		$this->smarty->assign("tipos_permissoes", getEnum('documentos', 'permissao'));
		$this->smarty->assign("grupos", $grupos);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/documento/cadastrar_documento.tpl');
	}

	/**
	 * Alterar os dados do documento
	 */
	public function alterarDocumento($documento_id) {
		$usuario_sessao = $this->session->get('usuario');
		if(is_null($usuario_sessao) || !$this->regra->possuiRegra($usuario_sessao->usuario->id, 16)) {
			return redirect()->route('login');
		}
		// mensagem temporaria da sessao
		$data = $this->session->getFlashdata('data');
		if(!isset($data)) {
			$data['msg'] = "";
			$data['msg_type'] = "";
			$data['errors'] = [];
		}

		// Carrega reuniao
		$documento = $this->documento->find($documento_id);
		if(!$documento) {
			return redirect()->route('reuniao');
		}

		if($this->request->getMethod() === 'post') {
			$titulo = $this->request->getPost('titulo');
			$tipo_vinculo = $this->request->getPost('tipo_vinculo');
			$grupo_id = $this->request->getPost('grupo_id');
			$reuniao_id = $this->request->getPost('reuniao_id');
			$tipo_documento = $this->request->getPost('tipo_documento');
			$tipo_permissao = $this->request->getPost('tipo_permissao');
			$marca_dagua = $this->request->getPost('marca_dagua') == "sim" ? 1 : 0;
			$referencia_id = $tipo_vinculo == 'reunião' ? $reuniao_id : $grupo_id;

			// Persistir o documento e caminho no banco de dados
			$dados = (object) array(
				"nome" => $titulo,
				"tipo" => $tipo_documento,
				"vinculo" => $tipo_vinculo,
				"referencia_id" => $referencia_id,
				"status_id" => 1,
				"marca_dagua" => $marca_dagua,
				"permissao" => $tipo_permissao,
				"data_alteracao" => date('Y-m-d H:i:s'),
				"usuario_alteracao_id" => $usuario_sessao->usuario->id
			);

			$novo_documento = $this->documento->update($documento->id, $dados);
			if($novo_documento) {
				$data['msg'] = "Documento adicionado com sucesso";
				$data['msg_type'] = "primary";
				return redirect()->route('documento')->with('data', $data);
			} else {
				$data['msg'] = "Ocorreu um problema ao tentar adicionar o documento. Erros encontrados:";
				$data['msg_type'] = "danger";
				array_push($data['errors'], $novo_documento);
				$status = false;
			}
		}

		// Carrega todos os grupos ativos
		$grupos = $this->grupo->where('status_id', 1)->findAll();
		//
		if($documento->vinculo == 'reunião') {
			$reuniao = $this->reuniao->find($documento->referencia_id);
			$grupo_reuniao = $this->grupo->find($reuniao->grupo_id);
		}else {
			$reuniao = null;
			$grupo_reuniao = null;
		}

		$this->smarty->assign("grupo_reuniao", $grupo_reuniao);
		$this->smarty->assign("reuniao", $reuniao);
		$this->smarty->assign("tipos_documento", getEnum('documentos', 'tipo'));
		$this->smarty->assign("tipos_vinculos", getEnum('documentos', 'vinculo'));
		$this->smarty->assign("tipos_permissoes", getEnum('documentos', 'permissao'));
		$this->smarty->assign("grupos", $grupos);
		$this->smarty->assign("documento", $documento);
		$this->smarty->assign("data", $data);
		$this->smarty->assign("usuario_sessao", $usuario_sessao);
		$this->smarty->display($this->smarty->getTemplateDir(0) .'/documento/alterar_documento.tpl');
	}

	/**
	 * Remove um documento
	 */
	public function removerDocumento($documento_id) {
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

		// Carrega documento
		$documento = $this->documento->find($documento_id);
		if(!$documento) {
			return redirect()->route('reuniao');
		}else {
			$dados = (object) array(
				"status_id" => 4, // excluido
				"data_alteracao" => date('Y-m-d H:i:s'),
				"usuario_alteracao_id" => $usuario_sessao->usuario->id
			);
			$status = $this->documento->update($documento_id, $dados);
			if($status) {
				$data['msg'] = "Documento removido!";
				$data['msg_type'] = "primary";
			}else {
				$data['msg'] = "Documento não removido. Erros encontrados:";
				$data['msg_type'] = "danger";
				array_push($data['errors'], $documento_id);
				$status = false;
			}
			return redirect()->route('documento')->with('data', $data);
		}
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
				return $this->response->download("./documentos/{$documento->hash}.{$documento->extensao}", null)->setFileName("{$documento->nome_arquivo}");

			}else {
				$guesser = new RegexGuesser();
				$permite_converter_pdf = sistemaCFG(1);

				// Verifica se precisa converter para PDF 1.4
				if($guesser->guess(FCPATH."/documentos/{$documento->hash}.{$documento->extensao}") > 1.4) {
					if($permite_converter_pdf == 1) {
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
							$data['msg'] = "O arquivo precisa ser convertido para PDF 1.4 para ser baixado.(CODE: 0002)";
							$data['msg_type'] = "danger";
							return redirect()->route('documento')->with('data', $data);
						}
					}else {
						$data['msg'] = "O arquivo PDF não pode ser convertido no momento.(CODE: 0001)";
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

				$mpdf->Output("{$documento->nome_arquivo}", 'I');
			}
		}
	}
}