{extends file="../head.tpl"}
{block name=main}
	<div class="container">
		<div class="title">
			<h2>Meus Dados</h2>
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
		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item" role="presentation">
					<button class="nav-link active" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab" aria-controls="dados" aria-selected="false">Dados</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="presencas-tab" data-bs-toggle="tab" data-bs-target="#presencas" type="button" role="tab" aria-controls="presencas" aria-selected="false">Presenças / Justificativas</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="grupos-tab" data-bs-toggle="tab" data-bs-target="#grupos" type="button" role="tab" aria-controls="grupos" aria-selected="false">Grupos</button>
			</li>
		</ul>
		<div class="tab-content" id="myTabContent">
			<!-- DADOS ----------------------------------------------------------------------------- !-->
			<div class="tab-pane fade show active" id="dados" role="tabpanel" aria-labelledby="dados-tab">
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
									<strong class="infoTxt">{$usuario->status_id|statusNome}</strong>
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- PRESENÇAS ----------------------------------------------------------------------------- !-->
			<div class="tab-pane fade" id="presencas" role="tabpanel" aria-labelledby="presencas-tab">
				<div class="row m-1">
					<div class="col">
						<h4 class="text-center mb-4 text-primary">Últimas Reuniões</h1>
							<table class="table table-sm table-responsive table-striped table-bordered text-center align-middle table-responsive-stack">
								<thead class="table-primary">
									<tr>
										{foreach from=$usuario_presencas item=usuario_presenca}
											<th scope="col">{$usuario_presenca->titulo}</th>
										{/foreach}
									</tr>
								</thead>
								<tbody>
									<tr>
										{foreach from=$usuario_presencas item=usuario_presenca}
											{if $usuario_presenca->presenca_id == 7}
												<td data-label="{$usuario_presenca->titulo}" class="table-success fw-bold">P</td>
											{elseif $usuario_presenca->presenca_id == 8}
												<td data-label="{$usuario_presenca->titulo}" class="table-danger fw-bold">F</td>
											{elseif $usuario_presenca->presenca_id == 9}
												<td data-label="{$usuario_presenca->titulo}" class="table-warning fw-bold">J</td>
											{else}
												<td data-label="{$usuario_presenca->titulo}" class="fw-bold">-</td>
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
					</div>
				</div>
				<div class="text-center">
					<a class="btn btn-sm btn-outline-primary btn-cadastrar" href="{url_to('reuniao_relatorio_presenca')}">Visualizar todas as presenças</a>
				</div>
			</div>
			<!-- GRUPOS ----------------------------------------------------------------------------- !-->
			<div class="tab-pane fade" id="grupos" role="tabpanel" aria-labelledby="grupos-tab">
				<div class="row m-1">
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
					<a class="btn btn-sm btn-voltar" href="{base_url()}">Voltar</a>
				</div>
			</div>
		</div>
	</div>
{/block}