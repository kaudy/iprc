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
		<form method="post" id="formulario">
			<input type="hidden" name="acao" id="acao" value="" required>
			{csrf_field()}
			<div class="title">
				<h2>Reunião</h2>
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
					<label for="titulo">
						<span class="nameField">
							Titulo:
						</span>
						<p>
							<strong class="infoTxt">{$reuniao->titulo}</strong>
						</p>
					</label>
				</div>
				<div class="col-12">
					<label for="descricao">
						<span class="nameField">
							Texto/Descrição:
						</span>
						<p>
							<strong class="infoTxt">{$reuniao->descricao}</strong>
						</p>
					</label>
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
							<label for="status" class="label">Data e Hora</label>
							<p>
								<strong class="infoTxt">{$reuniao->data_reuniao|DataHoraConvertBrString}</strong>
							</p>
						</div>
					</div>
					<div class="col-md-3">
						<div class="outline">
							<label for="status" class="label">Status</label>
							<p>
								<strong class="infoTxt">{$reuniao->status|statusNome}</strong>
							</p>
						</div>
					</div>
					{if $presenca_usuario|@count > 0}
					<div class="col-md-3">
						<div class="outline">
							<label for="status" class="label">Sua Presença</label>
							<p>
								<strong class="infoTxt">{$presenca_usuario[0]->status|statusNome}</strong>
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
					<button class="nav-link active" id="grupos-tab" data-bs-toggle="tab" data-bs-target="#grupos" type="button" role="tab" aria-controls="grupos" aria-selected="true">Grupos</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button" role="tab" aria-controls="documentos" aria-selected="false">Documentos</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="presencas-tab" data-bs-toggle="tab" data-bs-target="#presencas" type="button" role="tab" aria-controls="presencas" aria-selected="false">Presenças / Justificativas</button>
				</li>
			</ul>
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane fade show active" id="grupos" role="tabpanel" aria-labelledby="grupos-tab">
					<div class="row m-1">
						<div class="col">
							<table class="table table-sm table-responsive table-striped">
								<thead class="thead-light">
									<tr>
										<th scope="col">#</th>
										<th scope="col">Grupo</th>
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
				<div class="tab-pane fade" id="documentos" role="tabpanel" aria-labelledby="documentos-tab">
					<div class="row m-1">
						<div class="col">
							<table class="table table-sm table-responsive table-striped">
								<thead class="thead-light">
									<tr>
										<th scope="col">#</th>
										<th scope="col">Documento</th>
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
										</tr>
									{/foreach}
								</tbody>
							</table>
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
												{$presenca->justificativa}
											</td>
											<td>
												{$presenca->status_nome}
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
						<a class="btn btn-outline-warning btn-sm" href="{base_url()}reuniao">Voltar</a>
					</div>
				</div>
			</div>
		</form>
	</div>
{/block}