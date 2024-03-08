{extends file="../head.tpl"}
{block name=main}
	<div class="container">
		<div class="row">
			<div class="col-md-11">
				<h2>Votações</h2>
			</div>
			<div class="col-md-1">
			<a class="btn btn-outline-primary btn-sm" href="{url_to('votacao_cadastar')}">Cadastrar</a>
			</div>
		</div>
		<br>
		{if $data['msg'] != null && $data['msg'] != ''}
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
		<br>
		<form class="" method="post" onsubmit="">
			{csrf_field()}
			<div class="row">
				<div class="col">
					<div class="form-outline">
						<div class="row">
							<div class="col-md-6">
								<label for="titulo">Titulo</label>
								<input type="text" class="form-control" id="titulo" name="titulo" placeholder="Titulo" value="{if $smarty.post}{$smarty.post.titulo}{/if}">
							</div>
							<div class="col-md-3">
								<label for="titulo">Status</label>
								<select class="form-control" name="tipo_status_id" id="tipo_status_id">
									<option value="">Selecione</option>
									{foreach from=$tipos_status item=tipo_status}
										<option value="{$tipo_status->id}" {if $smarty.post}{if $smarty.post.tipo_status_id == $tipo_status->id}selected{/if}{/if}>{$tipo_status->nome|ucfirst}</option>
									{/foreach}
								</select>
							</div>
							<div class="col-md-3 row">
								<label for="titulo"></label>
								<button class="btn btn-primary btn-sm" type="submit">Pesquisar</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
		<br>
			<div style="height: 1px;background-color:grey"></div>
		<br>
		<div class="row">
			<table cellspacing="1" summary="Listagem de todos usuários" class="table table-sm table-responsive table-striped">
				<thead  class="thead-light">
					<tr>
						<th scope="col">
							#
						</th>
						<th scope="col">
							Votação
						</th>
						<th scope="col">
							Escolhas
						</th>
						<th scope="col">
							Opções
						</th>
						<th scope="col">
							Status
						</th>
						<th scope="col">
							Ações
						</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$votacoes item=registro}
						<tr>
							<td data-title="#">
								{$registro@iteration}
							</td>
							<td data-title="Título">
								{$registro->titulo}
							</td>
							<td data-title="Qtde. Escolhas" class="center">
								{$registro->qtd_escolhas}
							</td>
							<td data-title="Qtde. Opções">
								{$registro->qtde_opcoes}
							</td>

							<td data-title="Status">
								{$registro->status|ucfirst}
							</td>
							<td data-title="Ações">
								{if $registro->permite_votar == true}
									<a class="btn btn-primary btn-sm" href="{url_to('votacao_votar', $registro->id)}">Votar</a>
								{/if}
								{if $registro->status_id == 3}
									<a class="btn btn-primary btn-sm" href="{url_to('votacao_ativar', $registro->id)}">Ativar</a>
									<a class="btn btn-outline-primary btn-sm" href="{url_to('votacao_alterar', $registro->id)}">Alterar</a>
									<a class="btn btn-outline-primary btn-sm" href="{url_to('votacao_cadastar_opcoes', $registro->id)}">Opções</a>
								{/if}
								<a class="btn btn-outline-secondary btn-sm" href="{url_to('votacao_visualizar', $registro->id)}">Visualizar</a>
								<a class="btn btn-outline-danger btn-sm" href="{url_to('votacao_cancelar', $registro->id)}">Cancelar</a>
							</td>
						</tr>
					{foreachelse}
						<tr>
							<td colspan="6" class="center">
								<p>Registro indisponível</p>
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	</div>
{/block}