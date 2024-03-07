{extends file="../head.tpl"}
{block name=main}
	<script>
		$(document).ready(function() {

		});
	</script>
	<div class="container">
		<div class="title">
			<h2>Votação - Cadastrar Fiscais</h2>
		</div>
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
		<form class="was-validated" method="post" onsubmit="">
			{csrf_field()}
			<br>
			<div class="row">
				<div class="col-md-12">
					<div class="form-outline">
						<label for="grupo_id" class="form-label">Fiscal</label>
						<div class="row">
							<div class="col">
								<select class="form-control" name="usuario_id" id="usuario_id">
									<option value="">Selecione</option>
									{foreach from=$fiscais item=fiscal}
										<option value="{$fiscal->id}">{$fiscal->nome|ucfirst}</option>
									{/foreach}
								</select>
							</div>
							<div class="col">
								<button class="btn btn-primary btn-sm" type="submit">Adicionar</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<br>
			<div style="height: 1px;background-color:grey"></div>
			<br>
			<div class="row" id="listagem_votacao_fiscal">
				<div class="col">
					<label for="grupo_id" class="form-label">Fiscais Cadastrados</label>
					<table id="tabela_listagem_votacao_fiscal" cellspacing="1" class="table table-sm table-responsive table-striped">
						<thead class="thead-light">
							<tr>
								<th>
								#
								</th>
								<th>
									Fiscal
								</th>
								<th>
									Ação
								</th>
							</tr>
						</thead>
						<tbody>
							{foreach from=$votacao_fiscais item=fiscal}
								<tr>
									<td scope="row">
										{$fiscal@iteration}
									</td>
									<td>
										{$fiscal->nome}
									</td>
									<td>
										<a class="btn btn-outline-danger btn-sm" href="{url_to('votacao_remover_fiscal', $votacao->id, $fiscal->id)}">Remover</a>
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
			</div>
			<br>
			<div style="height: 1px;background-color:grey"></div>
			<br>
			<a class="btn btn-outline-warning btn-sm" href="{url_to('votacao_cadastar_grupos', $votacao->id)}">Voltar</a>
			<a class="btn btn-outline-primary btn-sm" href="{url_to('votacao_visualizar', $votacao->id)}">Próximo >></a>
		</form>
	</div>
{/block}