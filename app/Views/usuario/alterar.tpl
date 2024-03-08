{extends file="../head.tpl"}
{block name=main}
	<script>
		var gruposSelecionados = [];
		var gruposSelecionadosNomes = [];

		$(document).ready(function() {
			verificaTipoDocumento();
			// Carrega os grupos do usuário
			{foreach from=$usuario_grupos item=usuario_grupo}
				gruposSelecionados.push({$usuario_grupo->grupo_id});
				gruposSelecionadosNomes.push('{$usuario_grupo->grupo_nome}');
			{/foreach}
			atualizaListaGrupos();

			var SPMaskBehavior = function(val) {
					return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
				},
				spOptions = {
					onKeyPress: function(val, e, field, options) {
						field.mask(SPMaskBehavior.apply({}, arguments), options);
					}
				};

			$('#telefone').mask(SPMaskBehavior, spOptions);
		});

		function verificaTipoDocumento() {
			var tipoDocumento = document.getElementById("tipo_documento").value;
			var documento = document.getElementById("documento").value;
			if (tipoDocumento == 'CPF') {
				$('#documento').mask('000.000.000-00');
			} else if (tipoDocumento == 'CNPJ') {
				$('#documento').mask('00.000.000/0000-00');
			} else {
				$('#documento').unmask();
			}
		}

		// Adiciona elementos no grupo de array
		function adicionaGrupo() {
			var grupoID = $('#grupo_id').val();
			var nome = $('#grupo_id option:selected').text();
			if (grupoID != '' && grupoID != null) {
				var found = gruposSelecionados.find((element) => element == grupoID);
				if (!found) {
					gruposSelecionados.push(grupoID);
					gruposSelecionadosNomes.push(nome);
				}
			}
			atualizaListaGrupos();
		}

		// Remove os elementos do grupo de array
		function removeGrupo(item) {
			const index = gruposSelecionados.indexOf(item);
			gruposSelecionados.splice(index, 1);
			gruposSelecionadosNomes.splice(index, 1);
			atualizaListaGrupos();
		}

		// Atualiza o dados na tabela de exibição
		function atualizaListaGrupos() {
			$('#usuario_grupos').val(gruposSelecionados);

			if (gruposSelecionados.length > 0) {
				var content = '';
				for (let index = 0; index < gruposSelecionadosNomes.length; index++) {
					content += "<tr>";
					content += "<td>" + gruposSelecionadosNomes[index] +"</td>";
					content += '<td> <a class="btn btn-outline-danger btn-sm" onclick="removeGrupo('+ gruposSelecionados[index] + ');">Remover</a></td>';
					content += "</tr>";
				}
				$('#tabela_listagem_usuario_grupo tbody').empty().append(content);
				$('#tabela_listagem_usuario_grupo').removeAttr("hidden");
				$('#listagem_usuario_grupo').removeAttr("hidden");
			} else {
				$('#tabela_listagem_usuario_grupo tbody').empty();
				$('#tabela_listagem_usuario_grupo').attr("hidden");
				$('#listagem_usuario_grupo').attr("hidden");
			}
		}
	</script>
	<div class="container">
		<div class="title">
			<h2>Alterar Usuário</h2>
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
		<form class="was-validated" method="post" onsubmit="">
			{csrf_field()}
			<div class="row">
				<div class="col-12">
					<label for="nome">Nome</label>
					<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome Completo"
						value="{$pessoa->nome}" required>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					<div class="form-outline">
						<label for="tipo_documento" class="form-label">Tipo Documento</label>
						<select class="form-control" name="tipo_documento" id="tipo_documento" required
							onchange="verificaTipoDocumento();">
							<option value="">Selecione</option>
							{foreach from=$tipos_documento item=tipo_documento}
								<option value="{$tipo_documento}" {if $pessoa->tipo_documento == $tipo_documento}selected {/if}>
									{$tipo_documento}
								</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-outline">
						<label for="documento cpf" class="form-label">Nº Documento</label>
						<input type="text" class="form-control" id="documento" name="documento" placeholder="Documento"
							value="{$pessoa->documento}" required>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-outline">
						<label for="data_nascimento" class="form-label">Data Nasc.</label>
						<input id="data_nascimento" name="data_nascimento" class="form-control" type="date"
							value="{$pessoa->data_nascimento}" required />
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-outline">
						<label for="sexo" class="form-label">Sexo</label>
						<select class="form-control" id="sexo" name="sexo" required>
							<option value="">Selecione</option>
							{foreach from=$tipos_sexos item=sexo}
								<option value="{$sexo}" {if $pessoa->sexo == $sexo}selected {/if}>{$sexo|ucfirst}
								</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-outline">
						<label for="estado_civil" class="form-label">Estado Civil</label>
						<select class="form-control" name="estado_civil" id="estado_civil" required>
							<option value="">Selecione</option>
							{foreach from=$estados_civil item=estado_civil}
								<option value="{$estado_civil}" {if $pessoa->estado_civil == $estado_civil}selected {/if}>
									{$estado_civil|ucfirst}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-outline">
						<label for="telefone" class="form-label">Telefone</label>
						<input type="text" class="form-control" id="telefone" name="telefone"
							placeholder="Preferencial celular" value="{$pessoa->telefone}" required>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="form-outline">
					<label for="email" class="form-label">Email</label>
					<input type="email" class="form-control" name="email" id="email" placeholder="Email válido"
						value="{$pessoa->email}" required>
				</div>
			</div>
			<div class="row">
				<div class="form-outline">
					<label for="senha" class="form-label">Senha</label>
					<input type="password" class="form-control" id="senha" name="senha" placeholder="Senha">
					<div class="valid-feedback">
						Se campo senha for mantido vazio, não será substituido a senha
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-{if $usuario->status != 3}4{else}12{/if}">
					<div class="form-outline">
						<label for="perfil_id" class="form-label">Perfil</label>
						<select class="form-control" name="perfil_id" id="perfil_id" required>
							<option value="">Selecione</option>
							{foreach from=$perfis item=perfil}
								<option value="{$perfil->id}" {if $usuario->perfil_id ==$perfil->id}selected {/if}>
									{$perfil->nome}</option>
							{/foreach}
						</select>
					</div>
				</div>
				{if $usuario->status != 3}
					<div class="col-md-8">
						<div class="form-outline">
							<label for="tipo_status_id" class="form-label">Status</label>
							<select class="form-control" name="tipo_status_id" id="tipo_status_id" required>
								<option value="">Selecione</option>
								{foreach from=$tipos_status item=tipo_status}
									<option value="{$tipo_status->id}" {if $usuario->status == $tipo_status->id}selected {/if}>
										{$tipo_status->nome}</option>
								{/foreach}
							</select>
						</div>
					</div>
				{/if}
			</div>
			<br>
			<div style="height: 1px;background-color:grey"></div>
			<br>
			<div class="row">
				<div class="col-md-6">
					<div class="form-outline">
						<label for="grupo_id" class="form-label">Grupos</label>
						<div class="row">
							<div class="col">
								<input type="hidden" name="usuario_grupos" id="usuario_grupos" value="[]">
								<select class="form-control" name="grupo_id" id="grupo_id">
									<option value="">Selecione</option>
									{foreach from=$grupos item=grupo}
										<option value="{$grupo->id}">{$grupo->nome|ucfirst}</option>
									{/foreach}
								</select>
							</div>
							<div class="col">
								<a class="btn btn-primary btn-sm" onclick="adicionaGrupo();">Adicionar</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row" id="listagem_usuario_grupo" hidden>
				<div class="col">
					<table id="tabela_listagem_usuario_grupo" cellspacing="1" class="table table-sm table-responsive table-striped" hidden>
						<thead class="thead-light">
							<tr>
								<td>
									Grupo
								</td>
								<td>
									Ação
								</td>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
			<br>
			<div style="height: 1px;background-color:grey"></div>
			<br>
			<button class="btn btn-primary btn-sm" type="submit">Alterar</button>
			<a class="btn btn-outline-warning btn-sm" href="{base_url()}usuario">Voltar</a>
		</form>
	</div>
{/block}