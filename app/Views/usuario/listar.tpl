{extends file="../head.tpl"}
{block name=main}
	<div class="container">
		<div class="row">
			<div class="col-md-11">
				<h2>Usuários</h2>
			</div>
			<div class="col-md-1">
			<a class="btn btn-outline-primary btn-sm" href="{url_to('usuario_cadastar')}">Cadastrar</a>
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
		<div class="row">
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
					{foreach from=$usuarios item=registro}
						<tr>
							<td data-title="Usuário">
								{$registro->id}
							</td>
							<td data-title="Usuário">
								{$registro->nome}
							</td>
							<td data-title="Status">
								{$registro->status}
							</td>
							<td data-title="Ações">
								<a class="btn btn-outline-primary btn-sm"
									href="{url_to('usuario_alterar', $registro->id)}">Alterar</a>
								<a class="btn btn-outline-secondary btn-sm"
									href="{url_to('usuario_visualizar', $registro->id)}">Visualizar</a>
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
		</div>
	</div>
{/block}