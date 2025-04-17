{extends file="../head.tpl"}
{block name=main}
	<script>
		$(document).ready(function() {

		});
	</script>
	<div class="container">
		<div class="title">
			<h2>Votação - Cadastrar opções de votos</h2>
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
						<label for="grupo_id" class="form-label">Opção</label>
						<div class="row">
							<div class="col-md-8">
								<input type="text" class="form-control" id="opcao" name="opcao" placeholder="Opção" required>
							</div>
							<div class="col">
								<button class="btn btn-sm btn-cadastrar" type="submit">Adicionar</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<br>
			<div style="height: 1px;background-color:grey"></div>
			<br>
			<div class="row" id="listagem_votacao_grupo">
				<div class="col">
					<label for="grupo_id" class="form-label">Opção Cadastradas</label>
					<table id="tabela_listagem_votacao_grupo" cellspacing="1" class="table table-sm table-responsive table-striped">
						<thead class="thead-light">
							<tr>
								<th>
								#
								</th>
								<th>
									Opção
								</th>
								<th>
									Ação
								</th>
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
									<td>
										<a class="btn btn-outline-danger btn-sm" href="{url_to('votacao_remover_opcao', $opcao->votacao_id, $opcao->id)}">Remover</a>
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
			<a class="btn btn-sm btn-voltar" href="{url_to('votacao_alterar', $votacao->id)}">Voltar</a>
			<a class="btn btn-sm btn-cadastrar" href="{url_to('votacao_cadastar_grupos', $votacao->id)}">Próximo >></a>
		</form>
	</div>
{/block}