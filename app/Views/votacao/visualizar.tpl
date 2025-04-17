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
				<h2>Votação</h2>
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
					<label for="nome">
						<span class="nameField">
							Titulo:
						</span>
						<p>
							<strong class="infoTxt">{$votacao->titulo}</strong>
						</p>
					</label>
				</div>
				<div class="col-12">
					<label for="nome">
						<span class="nameField">
							Texto/Descrição:
						</span>
						<p>
							<strong class="infoTxt">{$votacao->texto|capitalize}</strong>
						</p>
					</label>
				</div>
				<div class="row">
					<div class="col-md-3">
						<div class="outline">
							<label for="qtd_escolhas" class="label">
							Qtde. Escolhas
							</label>
							<p>
								<strong class="infoTxt">{$votacao->qtd_escolhas}</strong>
							</p>
						</div>
					</div>
					<div class="col-md-3">
						<div class="outline">
							<label for="status_id" class="label">Status</label>
							<p>
								<strong class="infoTxt">{$votacao->status_id|statusNome}</strong>
							</p>
						</div>
					</div>
				</div>
			</div>
			<div style="height: 1px;background-color:grey"></div>
			<br>
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item" role="presentation">
				<button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#votos" type="button" role="tab" aria-controls="votos" aria-selected="true">Opções</button>
				</li>
				<li class="nav-item" role="presentation">
				<button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#grupos" type="button" role="tab" aria-controls="grupos" aria-selected="false">Grupos</button>
				</li>
				<li class="nav-item" role="presentation">
				<button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#fiscais" type="button" role="tab" aria-controls="fiscais" aria-selected="false">Fiscais</button>
				</li>
			</ul>
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane fade show active" id="votos" role="tabpanel" aria-labelledby="votos-tab">
					<div class="row m-1">
						<div class="col">
							<table class="table table-sm table-responsive table-striped">
								<thead class="thead-light">
									<tr>
										<th scope="col">#</th>
										<th scope="col">Opção</th>
									</tr>
								</thead>
								<tbody>
									{foreach from=$votacao_opcoes item=opcao}
										<tr>
											<td scope="row">
												{$opcao@iteration}
											</td>
											<td scope="row">
												{$opcao->titulo|capitalize}
											</td>
										</tr>
									{/foreach}
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="grupos" role="tabpanel" aria-labelledby="grupos-tab">
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
									{foreach from=$votacao_grupos item=votacao_grupo}
										<tr>
											<td scope="row">
												{$votacao_grupo@iteration}
											</td>
											<td>
												{$votacao_grupo->grupo_nome|capitalize}
											</td>
										</tr>
									{/foreach}
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="fiscais" role="tabpanel" aria-labelledby="fiscais-tab">
					<div class="row m-1">
						<div class="col">
							<table class="table table-sm table-responsive table-striped">
								<thead class="thead-light">
									<tr>
										<th scope="col">#</th>
										<th scope="col">Fiscal</th>
									</tr>
								</thead>
								<tbody>
									{foreach from=$votacao_fiscais item=votacao_fiscal}
										<tr>
											<td scope="row">
												{$votacao_fiscal@iteration}
											</td>
											<td>
												{$votacao_fiscal->nome}
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
						{if $votacao->status_id == 3}
							<a class="btn btn-sm btn-cadastrar" href="{base_url()}votacao">Confirmar</a>
							<a class="btn btn-sm btn-cadastrar" href="{url_to('votacao_ativar', $votacao->id)}">Ativar</a>
							<a class="btn btn-outline-primary btn-cadastrar" href="{url_to('votacao_alterar', $votacao->id)}">Alterar</a>
						{/if}
						<a class="btn btn-sm btn-voltar" href="{base_url()}votacao">Voltar</a>
					</div>
				</div>
			</div>
		</form>
	</div>
{/block}