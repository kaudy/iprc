{extends file="../head.tpl"}
{block name=main}
	<script>
		$(document).ready(function() {
			verificaTipoDocumento();

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
	</script>
	<div class="container">
		<div class="title">
			<h2>Adicionar Usuário</h2>
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
					<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome Completo" required>
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
								<option value="{$tipo_documento}">{$tipo_documento}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-outline">
						<label for="documento cpf" class="form-label">Nº Documento</label>
						<input type="text" class="form-control" id="documento" name="documento" placeholder="Documento"
							required>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-outline">
						<label for="data_nascimento" class="form-label">Data Nasc.</label>
						<input id="data_nascimento" name="data_nascimento" class="form-control" type="date" required />
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-outline">
						<label for="sexo" class="form-label">Sexo</label>
						<select class="form-control" id="sexo" name="sexo" required>
							<option value="">Selecione</option>
							{foreach from=$tipos_sexos item=sexo}
								<option value="{$sexo}">{$sexo|ucfirst}</option>
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
								<option value="{$estado_civil}">{$estado_civil|ucfirst}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-outline">
						<label for="telefone" class="form-label">Telefone</label>
						<input type="text" class="form-control" id="telefone" name="telefone"
							placeholder="Preferencial celular" required>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="form-outline">
					<label for="email" class="form-label">Email</label>
					<input type="email" class="form-control" name="email" id="email" placeholder="Email válido" required>
				</div>
			</div>
			<div class="row">
				<div class="form-outline">
					<label for="senha" class="form-label">Senha</label>
					<input type="password" class="form-control" id="senha" name="senha" placeholder="Senha">
					<div class="valid-feedback">
						Se campo senha for mantido vazio será gerado uma senha aleatória para usuario no primeiro login
						trocar.
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-outline">
						<label for="perfil_id" class="form-label">Perfil</label>
						<select class="form-control" name="perfil_id" id="perfil_id" required>
							<option value="">Selecione</option>
							{foreach from=$perfis item=perfil}
								<option value="{$perfil->id}">{$perfil->nome}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
			<br>
			<button class="btn btn-primary btn-sm" type="submit">Cadastrar</button>
			<a class="btn btn-outline-warning btn-sm" href="{base_url()}usuario">Voltar</a>
		</form>
	</div>
{/block}