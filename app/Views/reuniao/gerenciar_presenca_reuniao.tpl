{extends file="../head.tpl"}
{block name=main}
	<div class="container">
		<div class="row">
			<div class="col-md-9">
				<h2>Reunião #{$reuniao->id} - Gerenciar Presenças</h2>
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
		<div class="row">
			<table cellspacing="1" summary="Listagem de presenças" class="table table-sm table-responsive table-striped">
				<thead  class="thead-light">
					<tr>
						<th scope="col">
							#
						</th>
						<th scope="col">
							Nome
						</th>
						<th scope="col">
							Justificativa
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
					{foreach from=$participantes_reuniao item=registro}
						<tr>
							<td data-title="ID">
								{$registro@iteration}
							</td>
							<td data-title="Usuário">
								{$registro->pessoa_nome}
							</td>
							<td data-title="Grupo">
								{$registro->justificativa|textoEncurtado:50}
							</td>
							<td data-title="Status">
								<i class="bi bi-circle-fill {if $registro->presenca_status_nome}{$registro->presenca_status_nome}{/if}"></i>
								{if $registro->presenca_status_nome}{$registro->presenca_status_nome|capitalize}{/if}
							</td>
							<td data-title="Ações">
								<a class="btn btn-sm btn-justificar" href="{url_to('reuniao_presenca_justificar', $reuniao->id, $registro->id)}">Justificar</a>
								<a class="btn btn-sm btn-votar" href="{url_to('reuniao_presenca_confirmar', $reuniao->id, $registro->id, 'presente')}">Presente</a>
								<a class="btn btn-sm btn-cancelar" href="{url_to('reuniao_presenca_confirmar', $reuniao->id, $registro->id, 'ausente')}">Ausente</a>
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
		<div class="row">
			<div class="row">
				<div class="col-md-1">
					<p>
						<strong class="infoTxt">Ações</strong>
					</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<a class="btn btn-outline-warning btn-sm" href="{base_url()}reuniao">Voltar</a>
				</div>
			</div>
		</div>
	</div>
{/block}