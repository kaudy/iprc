{extends file="../head.tpl"}
{block name=main}
	<div class="container">
		<div class="row">
			<div class="col-md-11">
				<h2>Votações</h2>
			</div>
			<div class="col-md-1">
				{if $permite_cadastrar_votacao == true}
					<a class="btn btn-sm btn-outline-primary btn-cadastrar" href="{url_to('votacao_cadastar')}">Cadastrar</a>
				{/if}
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
								<input type="text" class="form-control" id="titulo" name="titulo" placeholder="Titulo"
									value="{if $smarty.post}{$smarty.post.titulo}{/if}">
							</div>
							<div class="col-md-3">
								<label for="titulo">Status</label>
								<select class="form-control" name="tipo_status_id" id="tipo_status_id">
									<option value="">Selecione</option>
									{foreach from=$tipos_status item=tipo_status}
										<option value="{$tipo_status->id}"
											{if $smarty.post}{if $smarty.post.tipo_status_id == $tipo_status->id}selected{/if}{/if}>
											{$tipo_status->nome|ucfirst}</option>
									{/foreach}
								</select>
							</div>
							<div class="col-md-2 div-pesquisar">
								<button class="btn btn-primary btn-sm btn-pesquisar" type="submit">Pesquisar</button>
							</div>
							<div class="col-md-1 div-pesquisar">
								<button class="btn btn-sm bi btn-filtro bi-funnel" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltros"  data-bs-placement="top" title="Mais filtros" aria-expanded="false" aria-controls="collapseExample"></button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="collapse" id="collapseFiltros">
				<div class="row">
					<div class="col">
						Mais filtros
					</div>
				</div>
			</div>
		</form>
		<br>
		<div style="height: 1px;background-color:grey"></div>
		<br>
		<div class="row">
			<table cellspacing="1" summary="Listagem de todos usuários"
				class="table table-sm table-responsive table-striped">
				<thead class="thead-light">
					<tr>
						<th scope="col">
							#
						</th>
						<th scope="col">
							Votação
						</th>
						<th scope="col" class="priority-5">
							Escolhas
						</th>
						<th scope="col" class="priority-4">
							Opções
						</th>
						<th scope="col">
							Status
						</th>
						<th scope="col" class="center">
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
							<td data-title="Qtde. Escolhas" class="priority-5">
								{$registro->qtd_escolhas}
							</td>
							<td data-title="Qtde. Opções" class="priority-4">
								{$registro->qtde_opcoes}
							</td>

							<td data-title="Status">
								<i class="bi bi-circle-fill {$registro->status_nome}"></i>
								{$registro->status_nome|ucfirst}
							</td>
							<td data-title="Ações">
								{if $registro->permite_resultado == true}
									<a class="btn btn-sm btn-votar" href="{url_to('votacao_resultado', $registro->id)}">Resultado</a>
								{/if}
								{if $registro->permite_finalizar == true}
									<a class="btn btn-sm btn-votar" href="{url_to('votacao_finalizar', $registro->id)}">Finalizar</a>
								{/if}
								{if $registro->permite_votar == true}
									<a class="btn btn-sm btn-votar" href="{url_to('votacao_votar', $registro->id)}">Votar</a>
								{/if}
								{if $registro->permite_alterar == true}
									<a class="btn btn-sm btn-ativar" href="{url_to('votacao_ativar', $registro->id)}">Ativar</a>
								{/if}
								{if $registro->permite_alterar == true}
									<a class="btn btn-sm btn-alterar"
										href="{url_to('votacao_alterar', $registro->id)}">Alterar</a>
									<a class="btn btn-sm btn-alterar priority-5"
										href="{url_to('votacao_cadastar_opcoes', $registro->id)}">Opções</a>
								{/if}
								<a class="btn btn-sm btn-outline-secondary btn-visualizar"
									href="{url_to('votacao_visualizar', $registro->id)}">Visualizar</a>
								{if $registro->permite_cancelar == true}
									<a class="btn btn-sm btn-outline-danger btn-cancelar"
									href="{url_to('votacao_cancelar', $registro->id)}">Cancelar</a>
								{/if}
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