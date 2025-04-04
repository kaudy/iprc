{extends file="../head.tpl"}
{block name=main}
	<div class="container">
		<div class="row">
			<div class="col-md-11">
				<h2>Documentos</h2>
			</div>
			{if $permite_cadastrar_documento == true}
				<div class="col-md-1">
					<a class="btn btn-sm btn-outline-primary btn-cadastrar" href="{url_to('documento_cadastar')}">Cadastrar</a>
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
								<label for="nome" class="label">Nome</label>
								<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome"
									value="{if $smarty.post}{$smarty.post.nome}{/if}">
							</div>
							<div class="col-md-2">
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
							<div class="col-md-2">
								<label for="tipo_documento" class="label">Tipo de Documento</label>
								<select class="form-control" name="tipo_documento" id="tipo_documento">
									<option value="">Selecione</option>
									{foreach from=$tipos_documento item=tipo_documento}
										<option value="{$tipo_documento}" {if $smarty.post}{if $smarty.post.tipo_documento == $tipo_documento}selected{/if}{/if}>
											{$tipo_documento|ucfirst}
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
								<label for="data_inicial" class="form-label">Data Inicial</label>
								<input id="data_inicial" name="data_inicial" class="form-control" type="date" value="{if $smarty.post}{if $smarty.post.data_inicial}{$smarty.post.data_inicial}{/if}{/if}"/>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="col-md-3">
							<div class="form-outline">
								<label for="data_final" class="form-label">Data Final</label>
								<input id="data_final" name="data_final" class="form-control" type="date" value="{if $smarty.post}{if $smarty.post.data_final}{$smarty.post.data_final}{/if}{/if}"/>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
		<br>
		<div style="height: 1px;background-color:grey"></div>
		<br>
		{if $documentos|count > 0}
			<div class="row">
				<table cellspacing="1" summary="Listagem de todos os arquivos" class="table table-sm table-responsive table-striped">
					<thead  class="thead-light">
						<tr>
							<th scope="col">
								#
							</th>
							<th scope="col">
								Nome
							</th>
							<th scope="col">
								Tipo
							</th>
							<th scope="col">
								Vinculo
							</th>
							<th scope="col">
								Data
							</th>
							<th scope="col">
								Ações
							</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$documentos item=registro}
							<tr>
								<td data-title="ID">
									{$registro->id}
								</td>
								<td data-title="Nome">
									{$registro->nome|ucfirst}
								</td>
								<td data-title="Tipo">
									{$registro->tipo|ucfirst}
								</td>
								<td data-title="Vinculo">
									{$registro->vinculo|ucfirst}
								</td>
								<td data-title="Data">
									{$registro->data_cadastro|DataHoraConvertBrString}
								</td>
								<td data-title="Ações">
									{if $registro->hash}
										<a class="bi bi-cloud-download-fill" href="{url_to('documento_download', $registro->hash)}" target="_blank"></a>
									{/if}
									<a class="bi bi-pencil-square" href="{url_to('documento_alterar', $registro->id)}"></a>
									<a class="bi bi-trash" href="{url_to('documento_remover', $registro->id)}"></a>
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
		{/if}
	</div>
{/block}