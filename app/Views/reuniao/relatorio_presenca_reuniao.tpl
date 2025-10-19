
{extends file="../head.tpl"}
{block name=main}
	<div class="container">
		<div class="row">
			<div class="col-md-11">
				<h2>Relatório - Presenças em Reuniões</h2>
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
			<div class="col">
				{if $presencas}
					<table class="table table-sm table-responsive table-striped table-bordered text-center align-middle table-responsive-stack">
						<thead class="table-primary">
							<tr>
								{foreach from=$presencas item=presenca}
									<th scope="col">{$presenca->titulo}</th>
								{/foreach}
							</tr>
						</thead>
						<tbody>
							<tr>
								{foreach from=$presencas item=presenca}
									{if $presenca->presenca_id == 7}
										<td data-label="{$presenca->titulo}" class="table-success fw-bold">P</td>
									{elseif $presenca->presenca_id == 8}
										<td data-label="{$presenca->titulo}" class="table-danger fw-bold">F</td>
									{elseif $presenca->presenca_id == 9}
										<td data-label="{$presenca->titulo}" class="table-warning fw-bold">J</td>
									{else}
										<td data-label="{$presenca->titulo}" class="fw-bold">-</td>
									{/if}
								{/foreach}
							</tr>
						</tbody>
					</table>
					<p class="text-center">
						Legenda:
						<span class="badge bg-success">P = Presente</span> |
						<span class="badge bg-danger">F = Falta</span> |
						<span class="badge bg-warning">J = Justificado</span>
					</p>
				{else}
					<p class="text-center">Nenhum registro encontrado.</p>
				{/if}

			</div>
		</div>
	</div>
{/block}