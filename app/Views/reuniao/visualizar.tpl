{extends file="../head.tpl"}
{block name=main}
	<script>
	$(document).ready(function() {


	});

	function submit(elemento, acao) {
		//elemento.preventDefault();
		// elemento.setAttribute('disabled', true);
		// console.log(acao);
		// $('#acao').val(acao);
		//$( "#formulario" ).submit();
		//$( "#formulario" ).trigger( "submit" );
	}
	</script>
	<div class="container">
		<form method="post" id="formulario" enctype="multipart/form-data">
			<input type="hidden" name="acao" id="acao" value="" required>
			{csrf_field()}
			<div class="title">
				<h2>Reunião > {$reuniao->titulo}</h2>
			</div>
			<br>
			<div style="height: 1px;background-color:grey"></div>
			<br>
			{if $data['msg'] != null}
				<div class="alert alert-{$data['msg_type']}" role="alert">
					{$data['msg']}
					{if $data['errors']|count > 0}
						<ul>
							{foreach from=$data['errors'] item=error}
								<li>{$error}</li>
							{/foreach}
						</ul>
					{/if}
				</div>
			{/if}
			<div class="row">
				<div class="col-12">
					<div class="accordion accordion-flush" id="accordionFlushExample">
						<div class="accordion-item">
								<h2 class="accordion-header" id="flush-headingOne">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="true" aria-controls="flush-collapseOne">
										Descrição:
									</button>
								</h2>
								<div id="flush-collapseOne" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
									<div class="accordion-body">
										{$reuniao->descricao}
									</div>
								</div>
							</div>
						</div>
					</div>
					<br>
					<br>
					<br>
				</div>
				<div class="row">
					<div class="col-md-3">
						<div class="outline">
							<label for="qtd_escolhas" class="label">
							Grupo Proprietário
							</label>
							<p>
								<strong class="infoTxt">{$grupo_proprietario->nome}</strong>
							</p>
						</div>
					</div>
					<div class="col-md-3">
						<div class="outline">
							<label for="data_hora" class="label">Data e Hora</label>
							<p>
								<strong class="infoTxt">{$reuniao->data_reuniao|DataHoraConvertBrString}</strong>
							</p>
						</div>
					</div>
					<div class="col-md-3">
						<div class="outline">
							<label for="status" class="label">Status</label>
							<p>
								<strong class="infoTxt">{$reuniao->status_id|statusNome}</strong>
							</p>
						</div>
					</div>
					{if $presenca_usuario|@count > 0}
						<div class="col-md-3">
							<div class="outline">
								<label for="presenca_status" class="label">Sua Presença</label>
								<p>
									<strong class="infoTxt">{$presenca_usuario[0]->status_id|statusNome}</strong>
								</p>
							</div>
						</div>
					{/if}
				</div>
			</div>
			<div style="height: 1px;background-color:grey"></div>
			<br>
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="grupos-tab" data-bs-toggle="tab" data-bs-target="#grupos" type="button" role="tab" aria-controls="grupos" aria-selected="true">Grupos</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link active" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button" role="tab" aria-controls="documentos" aria-selected="false">Documentos</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="presencas-tab" data-bs-toggle="tab" data-bs-target="#presencas" type="button" role="tab" aria-controls="presencas" aria-selected="false">Presenças / Justificativas</button>
				</li>
			</ul>
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane fade" id="grupos" role="tabpanel" aria-labelledby="grupos-tab">
					<div class="row m-1">
						<div class="col">
							<table class="table table-sm table-responsive table-striped">
								<thead class="thead-light">
									<tr>
										<th scope="col">#</th>
										<th scope="col">Grupo(s)</th>
									</tr>
								</thead>
								<tbody>
									{foreach from=$reuniao_grupos item=reuniao_grupo}
										<tr>
											<td scope="row">
												{$reuniao_grupo@iteration}
											</td>
											<td>
												{$reuniao_grupo->grupo_nome}
											</td>
										</tr>
									{/foreach}
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="tab-pane fade show active" id="documentos" role="tabpanel" aria-labelledby="documentos-tab">
					<div class="row m-1">
						<div class="col">
							<table class="table table-sm table-responsive table-striped">
								<thead class="thead-light">
									<tr>
										<th scope="col">#</th>
										<th scope="col">Documento(s)</th>
										<th scope="col">ações</th>
									</tr>
								</thead>
								<tbody>
									{foreach from=$reuniao_documentos item=reuniao_documento}
										<tr>
											<td scope="row">
												{$reuniao_documento@iteration}
											</td>
											<td>
												{$reuniao_documento->nome}
											</td>
											<td>
												{if $reuniao_documento->hash}
													<a class="btn btn-sm bi btn-download bi-cloud-download-fill" href="{url_to('documento_download', $reuniao_documento->hash)}" target="_blank"></a>
												{/if}
												{if $permite_remover_documento == true}
													<a class="btn btn-sm bi btn-excluir bi-trash" href="{url_to('reuniao_remover_documento', $reuniao->id, $reuniao_documento->id)}"></a>
												{/if}
											</td>
										</tr>
									{foreachelse}
										<tr>
											<td colspan="99" class="center" style="text-align: center;vertical-align: middle">
												<p>Não existem documentos vinculados a essa reunião.</p>
											</td>
										</tr>
									{/foreach}
								</tbody>
							</table>
							{if $permite_adicionar_documento == true}
								<br>
								<hr>
								<div class="mb-3">
									<label for="formFileSm" class="form-label">Adicionar documento a reunião</label>
									<input type="file" class="form-control form-control-sm" name="userfile" title="Adicionar documento">
									<input type="submit" value="upload" class="btn btn-primary btn-sm">
								</div>
							{/if}
						</div>
					</div>
				</div>
				<!-- PRESENÇAS ----------------------------------------------------------------------------- !-->
				<div class="tab-pane fade" id="presencas" role="tabpanel" aria-labelledby="presencas-tab">
					<div class="row m-1">
						<div class="col">
							<table class="table table-sm table-responsive table-striped">
								<thead class="thead-light">
									<tr>
										<th scope="col">#</th>
										<th scope="col">Nome</th>
										<th scope="col">Justificativa</th>
										<th scope="col">Status</th>
									</tr>
								</thead>
								<tbody>
									{foreach from=$presencas item=presenca}
										<tr>
											<td scope="row">
												{$presenca@iteration}
											</td>
											<td>
												{$presenca->pessoa_nome}
											</td>
											<td>
												{$presenca->justificativa|textoEncurtado:50}
											</td>
											<td>
												<i class="bi bi-circle-fill {if $presenca->status_nome}{$presenca->status_nome}{/if}"></i>
												{if $presenca->status_nome}{$presenca->status_nome|capitalize}{/if}
											</td>
										</tr>
									{/foreach}
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div style="height: 1px;background-color:grey"></div>
			<br>
			<div class="row">
				<div class="row">
					<div class="col-md-1">
						<p>
							<strong class="infoTxt">Ações</strong>
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
							{if $permite_confirmar == true}
								<a class="btn btn-primary btn-sm" href="{base_url()}reuniao">Confirmar</a>
							{/if}
							{if $permite_ativar == true}
								<a class="btn btn-primary btn-sm" href="{url_to('reuniao_ativar', $reuniao->id)}">Ativar</a>
							{/if}
							{if $permite_alterar == true}
								<a class="btn btn-outline-primary btn-sm" href="{url_to('reuniao_alterar', $reuniao->id)}">Alterar</a>
							{/if}
						<a class="btn btn-sm btn-voltar" href="{base_url()}reuniao">Voltar</a>
					</div>
				</div>
			</div>
		</form>
	</div>
{/block}