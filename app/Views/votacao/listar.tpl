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
			<table cellspacing="1" summary="Listagem de todos usuários" class="table table-sm table-responsive table-striped">
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
						<th scope="col" class="align-middle">
							Ações
						</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$votacoes item=registro}
						<tr>
							<td data-title="#" class="align-middle">
								{$registro@iteration}
							</td>
							<td data-title="Título" class="align-left">
								{$registro->titulo}
							</td>
							<td data-title="Qtde. Escolhas" class="priority-5 align-middle">
								{$registro->qtd_escolhas}
							</td>
							<td data-title="Qtde. Opções" class="align-middle priority-4">
								{$registro->qtde_opcoes}
							</td>

							<td data-title="Status" class="align-middle">
								<i class="bi bi-circle-fill {$registro->status_nome}"></i>
								{$registro->status_nome|ucfirst}
							</td>
							<td data-title="Ações" class="align-middle">
								{if $registro->permite_resultado == true}
									<a class="btn btn-sm bi btn-acao bi-file-text-fill" href="{url_to('votacao_resultado', $registro->id)}" title="Resultado"></a>
								{/if}
								{if $registro->permite_finalizar == true}
									<a class="btn btn-sm bi btn-acao bi-stop-circle-fill" href="{url_to('votacao_finalizar', $registro->id)}" title="Finalizar"></a>
								{/if}
								{if $registro->permite_votar == true}
									<a class="btn btn-sm bi btn-acao bi-envelope-paper-fill" href="{url_to('votacao_votar', $registro->id)}" title="Votar"></a>
								{/if}
								{if $registro->permite_alterar == true}
									<a class="btn btn-sm bi btn-acao bi-play-fill" href="{url_to('votacao_ativar', $registro->id)}" title="Ativar"></a>
								{/if}
								{if $registro->permite_alterar == true}
									<a class="btn btn-sm bi btn-alterar bi-pencil-square"
										href="{url_to('votacao_alterar', $registro->id)}" title="Alterar"></a>
									<a class="btn btn-sm bi btn-acao bi-pencil-square priority-5"
										href="{url_to('votacao_cadastar_opcoes', $registro->id)}" title="Opções"></a>
								{/if}
								<a class="btn btn-sm bi btn-visualizar bi-eye-fill"
									href="{url_to('votacao_visualizar', $registro->id)}" title="Visualizar"></a>
								{if $registro->permite_cancelar == true}
									<a class="btn btn-sm bi btn-excluir bi-trash"
									href="{url_to('votacao_cancelar', $registro->id)}" title="Cancelar"></a>
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