{extends file="../head.tpl"}
{block name=main}
	<div class="container">
		<div class="row">
			<div class="col-md-11">
				<h2>Reuniões</h2>
			</div>
			{if $permite_cadastrar_reuniao == true}
				<div class="col-md-1">
					<a class="btn btn-sm btn-outline-primary btn-cadastrar" href="{url_to('reuniao_cadastar')}">Cadastrar</a>
				</div>
			{/if}
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
							<div class="col-md-3">
								<label for="titulo" class="label">Nome</label>
								<input type="text" class="form-control" id="titulo" name="titulo" placeholder="Titulo"
									value="{if $smarty.post}{$smarty.post.titulo}{/if}">
							</div>
							<div class="col-md-3">
								<label for="titulo" class="label">Status</label>
								<select class="form-control" name="tipo_status_id" id="tipo_status_id">
									<option value="">Selecione</option>
									{foreach from=$tipos_status item=tipo_status}
										<option value="{$tipo_status->id}"
											{if $smarty.post}{if $smarty.post.tipo_status_id == $tipo_status->id}selected{/if}{/if}>
											{$tipo_status->nome|ucfirst}
										</option>
									{/foreach}
								</select>
							</div>
							<div class="col-md-3">
								<label for="grupo_id" class="label">Grupo</label>
								<select class="form-control" name="grupo_id" id="grupo_id">
									<option value="">Selecione</option>
									{foreach from=$grupos item=grupo}
										<option value="{$grupo->id}" {if $smarty.post}{if $smarty.post.grupo_id == $grupo->id}selected{/if}{/if}>
										{$grupo->nome|ucfirst}
									</option>
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
			<div class="collapse" id="collapseFiltros" name="collapseFiltros">
				<div class="row">
					<div class="col">
						<div class="col-md-3">
							<div class="form-outline">
								<label for="data_reuniao_inicial" class="form-label">Data Inicial</label>
								<input id="data_reuniao_inicial" name="data_reuniao_inicial" class="form-control" type="date" value="{if $smarty.post}{if $smarty.post.data_reuniao_inicial}{$smarty.post.data_reuniao_inicial}{/if}{/if}"/>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="col-md-3">
							<div class="form-outline">
								<label for="data_reuniao_final" class="form-label">Data Final</label>
								<input id="data_reuniao_final" name="data_reuniao_final" class="form-control" type="date" value="{if $smarty.post}{if $smarty.post.data_reuniao_final}{$smarty.post.data_reuniao_final}{/if}{/if}"/>
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
							Reunião
						</th>
						<th scope="col">
							Grupo
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
					{foreach from=$reunioes item=registro name=reg}
						<tr>
							<td data-title="ID" class="align-middle">
								{$smarty.foreach.reg.iteration}
							</td>
							<td data-title="Reunião" class="align-left">
								{$registro->titulo|textoEncurtado:50}
							</td>
							<td data-title="Grupo" class="align-left">
								{$registro->grupo_nome}
							</td>
							<td data-title="Status" class="align-middle">
								<i class="bi bi-circle-fill {$registro->status_nome}"></i>
								{$registro->status_nome|ucfirst}
							</td>
							<td data-title="Ações" class="align-middle">
								{if $permite_gerenciar_presencas == true}
									<a class="btn btn-sm bi btn-gerenciar bi-gear-fill" href="{url_to('reuniao_presenca_gerenciar', $registro->id)}" title="Gerenciar"></a>
								{/if}
								{if $registro->permite_justificar == true}
									<a class="btn btn-sm bi btn-justificar bi-check-square" href="{url_to('reuniao_justificar', $registro->id)}" title="Justificar"></a>
								{/if}
								{if $registro->permite_alterar == true}
									<a class="btn btn-sm bi btn-alterar bi-pencil-square" href="{url_to('reuniao_alterar', $registro->id)}" title="Alterar"></a>
								{/if}
								{if $registro->permite_ativar == true}
									<a class="btn btn-sm bi btn-acao bi-play-fill" href="{url_to('reuniao_ativar', $registro->id)}" title="Ativar"></a>
								{/if}
								<a class="btn btn-sm bi btn-visualizar bi-eye-fill" href="{url_to('reuniao_visualizar', $registro->id)}" title="Visualizar"></a>
								{if $registro->permite_cancelar == true}
									<a class="btn btn-sm bi btn-excluir bi-trash" href="{url_to('reuniao_cancelar', $registro->id)}" title="Cancelar"></a>
								{/if}
							</td>
						</tr>
					{foreachelse}
						<tr>
							<td colspan="99" class="null">
								<p>Registro indisponível</p>
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	</div>
{/block}