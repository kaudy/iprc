{extends file="../head.tpl"}
{block name=main}
	<div class="container">
		<div class="title">
			<h2>Usuário</h2>
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
		<div class="row">
			<div class="col-12">
				<label for="nome">
					<span class="nameField">
						Nome:
					</span>
					<p>
						<strong class="infoTxt">{$pessoa->nome}</strong>
					</p>
				</label>
			</div>
			<div class="row">
				<div class="col-md-3">
					<div class="outline">
						<label for="tipo_documento" class="label">
						Tipo Documento
						</label>
						<p>
							<strong class="infoTxt">{$pessoa->tipo_documento}</strong>
						</p>
					</div>
				</div>
				<div class="col-md-3">
					<div class="outline">
						<label for="documento cpf" class="label">Nº Documento</label>
						<p>
							<strong class="infoTxt">{$pessoa->documento|mascaraCpf}</strong>
						</p>
					</div>
				</div>
				<div class="col-md-3">
					<div class="outline">
						<label for="data_nascimento" class="label">Data Nasc.</label>
						<p>
							<strong class="infoTxt">{$pessoa->data_nascimento|DataConvertBr}</strong>
						</p>
					</div>
				</div>
				<div class="col-md-3">
					<div class="outline">
						<label for="sexo" class="label">Sexo</label>
						<p>
							<strong class="infoTxt">{$pessoa->sexo|ucfirst}</strong>
						</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="outline">
						<label for="estado_civil" class="label">Estado Civil</label>
						<p>
							<strong class="infoTxt">{$pessoa->estado_civil|ucfirst}</strong>
						</p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="outline">
						<label for="telefone" class="label">Telefone</label>
						<p>
							<strong class="infoTxt">{$pessoa->telefone}</strong>
						</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="outline">
						<label for="email" class="label">Email</label>
						<p>
							<strong class="infoTxt">{$pessoa->email}</strong>
						</p>
					</div>
				</div>
				<div class="col-md-3">
					<div class="outline">
						<label for="perfil_id" class="label">Perfil</label>
						<p>
							<strong class="infoTxt">{$perfil_usuario->nome}</strong>
						</p>
					</div>
				</div>
				<div class="col-md-3">
					<div class="outline">
						<label for="perfil_id" class="label">Status</label>
						<p>
							<strong class="infoTxt">{$usuario->status|statusNome}</strong>
						</p>
					</div>
				</div>
			</div>
		</div>
		<div style="height: 1px;background-color:grey"></div>
		<br>
		<div class="row">
			<div class="col">
				<label for="perfil_id" class="label">Grupos</label>
				<table class="table table-sm table-responsive table-striped">
				<title>Grupos</title>
					<thead class="thead-light">
						<tr>
							<th scope="col">#</th>
							<th scope="col">Grupo</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$usuario_grupos item=usuario_grupo}
							<tr>
								<td scope="row">
									{$usuario_grupo@iteration}
								</td>
								<td>
									{$usuario_grupo->grupo_nome}
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
					{if $usuario->status == 3}
						<a class="btn btn-outline-primary btn-sm" href="{url_to('usuario_reenviar_ativacao',$usuario->id)}">Reenviar Email Ativação</a>
					{/if}
					<a class="btn btn-outline-warning btn-sm" href="{base_url()}usuario">Voltar</a>
				</div>
			</div>
		</div>
	</div>
{/block}