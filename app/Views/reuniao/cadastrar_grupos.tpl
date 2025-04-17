{extends file="../head.tpl"}
{block name=main}
	<script>
		$(document).ready(function() {

		});
	</script>
	<div class="container">
		<div class="title">
			<h2>Reunião - Cadastrar Grupos Participantes</h2>
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
						<label for="grupo_id" class="form-label">Grupos</label>
						<div class="row">
							<div class="col-md-8">
							<select class="form-control" name="grupo_id" id="grupo_id">
								<option value="">Selecione</option>
								{foreach from=$grupos item=grupo}
									<option value="{$grupo->id}">{$grupo->nome|ucfirst}</option>
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
			<div class="row" id="listagem_reuniao_grupo">
				<div class="col">
					<label class="form-label">Grupos Cadastrados</label>
					<table id="tabela_listagem_votacao_grupo" cellspacing="1" class="table table-sm table-responsive table-striped">
						<thead class="thead-light">
							<tr>
								<th>
								#
								</th>
								<th>
									Grupo
								</th>
								<th>
									Ação
								</th>
							</tr>
						</thead>
						<tbody>
							{foreach from=$reuniao_grupos item=grupo}
								<tr>
									<td scope="row">
										{$grupo@iteration}
									</td>
									<td>
										{$grupo->grupo_nome}
									</td>
									<td>
										<a class="btn btn-outline-danger btn-sm" href="{url_to('reuniao_remover_grupo', $grupo->reuniao_id, $grupo->id)}">Remover</a>
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
			<a class="btn btn-sm btn-voltar" href="{url_to('reuniao_alterar', $reuniao->id)}">Voltar</a>
			<a class="btn btn-outline-primary btn-cadastrar" href="{url_to('reuniao_visualizar', $reuniao->id)}">Próximo</a>
		</form>
	</div>
{/block}