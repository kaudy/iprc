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
							<strong class="infoTxt">{$votacao->texto}</strong>
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
							<label for="status" class="label">Status</label>
							<p>
								<strong class="infoTxt">{$votacao->status|statusNome}</strong>
							</p>
						</div>
					</div>
				</div>
			</div>
			<div style="height: 1px;background-color:grey"></div>
			<br>
			<div class="row">
				<div class="col">
					<label for="perfil_id" class="label">Opções</label>
					<table class="table table-sm table-responsive table-striped">
					<title>Opções</title>
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
									<td>
										{$opcao->titulo}
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
			</div>
			<div style="height: 1px;background-color:grey"></div>
			<br>
			<div class="row">
				<div class="col">
					<label for="perfil_id" class="label">Grupos</label>
					<table class="table table-sm table-responsive table-striped">
					<title>Opções</title>
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
										{$votacao_grupo->grupo_nome}
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
			</div>
			<div style="height: 1px;background-color:grey"></div>
			<br>
			<div class="row">
				<div class="col">
					<label for="perfil_id" class="label">Fiscais</label>
					<table class="table table-sm table-responsive table-striped">
					<title>Opções</title>
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
						{if $votacao->status == 3}
							<a class="btn btn-primary btn-sm" href="{base_url()}votacao">Confirmar</a>
							<a class="btn btn-primary btn-sm" href="{url_to('votacao_ativar', $votacao->id)}">Ativar</a>
							<a class="btn btn-outline-primary btn-sm" href="{url_to('votacao_alterar', $votacao->id)}">Alterar</a>
						{/if}
						<a class="btn btn-outline-warning btn-sm" href="{base_url()}votacao">Voltar</a>
					</div>
				</div>
			</div>
		</form>
	</div>
{/block}