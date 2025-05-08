{extends file="../head.tpl"}
{block name=main}
	<script type="text/javascript">
		$(document).ready(function() {

		});

		function carregaReuniao() {
			let grupo_id = $('#grupo_id').val();
			let vinculo = $('#tipo_vinculo').val();

			if(vinculo == 'reunião' && grupo_id != '' && grupo_id != null && grupo_id != undefined) {
				$('#reuniao_id').attr('required', 'required');

				$.ajax({
					url: "{base_url()}/reuniao/listar_reunioes/" + grupo_id,
					type: 'GET',
					dataType: 'json',
					cache: false,
					headers: {
						'X-Requested-With': 'XMLHttpRequest'
					},
					success: function(data) {
						$('#reuniao_id').html('');
						var option = '<option value="">Selecione</option>';
						if (data.length != 0) {
							for (let index = 0; index < data.length; index++) {
								var element = data[index];
								option += '<option value="' + element.id + '">' + element.titulo + '</option>';
							}
						}
						$('#reuniao_id').html(option);
					}
				});
				$('#grupo_reuniao').removeAttr('hidden');
			} else {
				$('#reuniao_id').removeAttr('required');
				$('#grupo_reuniao').attr('hidden', 'hidden');
			}
		}
	</script>
	<div class="container">
		<div class="title">
			<h2>Cadastro de Documento</h2>
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
						<label for="titulo" class="form-label">Título</label>
						<input type="text" class="form-control" id="titulo" name="titulo" placeholder="Título" required>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-6">
					<div class="form-outline">
						<label for="tipo_vinculo" class="form-label">Vinculo</label>
						<select class="form-control" name="tipo_vinculo" id="tipo_vinculo" required onchange="carregaReuniao();">
							<option value="">Selecione</option>
							{foreach from=$tipos_vinculos item=tipo_documento}
								<option value="{$tipo_documento}">{$tipo_documento|ucfirst}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="col-6">
					<div class="form-outline">
						<label for="grupo_id" class="form-label">Grupo Responsável</label>
						<select class="form-control" name="grupo_id" id="grupo_id" required onchange="carregaReuniao();" >
							<option value="">Selecione</option>
							{foreach from=$grupos item=grupo}
								<option value="{$grupo->id}">{$grupo->nome|ucfirst}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
			<div class="row" id="grupo_reuniao" hidden>
				<div class="col-12">
					<div class="form-outline">
						<label for="reuniao_id" class="form-label">Reuniões</label>
						<select class="form-control" name="reuniao_id" id="reuniao_id">
							<option value="">Selecione</option>
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-4">
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
				<div class="col-4">
					<div class="form-outline">
						<label for="tipo_permissao" class="form-label">Permissões</label>
						<select class="form-control" name="tipo_permissao" id="tipo_permissao" required>
							<option value="">Selecione</option>
							{foreach from=$tipos_permissoes item=tipo_permissao}
								<option value="{$tipo_permissao}">{$tipo_permissao|ucfirst}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="col-4">
					<div class="mb-3 form-check" style="margin-top: 12px;">
						<br>
						<input class="form-check-input" type="checkbox" value="sim" name="marca_dagua" id="marca_dagua" checked>
						<label for="marca_dagua" class="form-check-label">Marca d'agua</label>
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
			<button class="btn btn-sm btn-outline-primary btn-cadastrar" type="submit">Cadastrar</button>
			<a class="btn btn-sm btn-voltar" href="{base_url()}documento">Voltar</a>
		</form>
	</div>
{/block}