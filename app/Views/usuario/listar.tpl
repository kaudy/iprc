{extends file="../head.tpl"}
{block name=main}
	<div class="container">
		<div class="row">
			<div class="col-md-11">
				<h2>Usuários</h2>
			</div>
			<div class="col-md-1">
			<a class="btn btn-sm btn-outline-primary btn-cadastrar" href="{url_to('usuario_cadastar')}">Cadastrar</a>
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
							<div class="col-md-3">
								<label for="nome">Nome</label>
								<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome"
									value="{if $smarty.post}{$smarty.post.nome}{/if}">
							</div>
							<div class="col-md-3">
								<label for="tipo_status_id">Status</label>
								<select class="form-control" name="tipo_status_id" id="tipo_status_id">
									<option value="">Selecione</option>
									{foreach from=$tipos_status item=tipo_status}
										<option value="{$tipo_status->id}"
											{if $smarty.post}{if $smarty.post.tipo_status_id == $tipo_status->id}selected{/if}{/if}>
											{$tipo_status->nome|ucfirst}</option>
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
			{if $usuarios}
				<table cellspacing="1" summary="Listagem de todos usuários" class="table table-sm table-responsive table-striped">
					<thead  class="thead-light">
						<tr>
							<th scope="col">
								#
							</th>
							<th scope="col">
								Usuário
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
						{foreach from=$usuarios item=registro name=reg}
							<tr>
								<td data-title="Usuário" class="align-middle">
									{$smarty.foreach.reg.iteration}
								</td>
								<td data-title="Usuário" class="align-left">
									{$registro->nome}
								</td>
								<td data-title="Status" class="align-middle">
									<i class="bi bi-circle-fill {$registro->status_nome}"></i>
									{$registro->status_nome|ucfirst}
								</td>
								<td data-title="Ações" class="align-middle">
									<a class="btn btn-sm bi btn-alterar bi-pencil-square" href="{url_to('usuario_alterar', $registro->id)}" title="Alterar"></a>
									<a class="btn btn-sm bi btn-visualizar bi-eye-fill" href="{url_to('usuario_visualizar', $registro->id)}" title="Visualizar"></a>
								</td>
							</tr>
						{foreachelse}
							<tr>
								<td colspan="4" class="null">
									<p>Registro indisponível</p>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			{/if}
		</div>
	</div>
{/block}