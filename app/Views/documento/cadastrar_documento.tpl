{extends file="../head.tpl"}
{block name=main}
	<script>
		$(document).ready(function() {

		});

	</script>
	<div class="container">
		<div class="title">
			<h2>Cadastrar Documento</h2>
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
		<form class="was-validated" method="post" enctype="multipart/form-data"">
			{csrf_field()}
			<div class="row">
				<div class="col-12">
					<div class="form-outline">
						<label for="grupo_id" class="form-label">Grupo Responsável</label>
						<select class="form-control" name="grupo_id" id="grupo_id" required>
							<option value="">Selecione</option>
							{foreach from=$grupos item=grupo}
								<option value="{$grupo->id}">{$grupo->nome|ucfirst}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="form-outline">
						<label for="tipo_documento" class="form-label">Tipo de Documento</label>
						<select class="form-control" name="tipo_documento" id="tipo_documento" required>
							<option value="">Selecione</option>
							{foreach from=$tipos_documento item=tipo_documento}
								<option value="{$tipo_documento}">{$tipo_documento|ucfirst}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<label for="texto">Arquivo</label>
					<input type="file" class="form-control form-control-sm" name="userfile" title="Adicionar documento" required>
				</div>
			</div>
			<br>
			<div style="height: 1px;background-color:grey"></div>
			<br>
			<button class="btn btn-primary btn-sm" type="submit">Cadastrar</button>
			<a class="btn btn-sm btn-voltar" href="{base_url()}documento">Voltar</a>
		</form>
	</div>
{/block}