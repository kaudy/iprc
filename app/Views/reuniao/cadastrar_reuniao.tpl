{extends file="../head.tpl"}
{block name=main}
	<script>
		$(document).ready(function() {
			CKEDITOR.replace('descricao');
		});

	</script>
	<div class="container">
		<div class="title">
			<h2>Cadastrar Reunião</h2>
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
					<label for="titulo">Titulo</label>
					<input type="text" class="form-control" id="titulo" name="titulo" placeholder="Titulo" required>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<label for="texto">Descrição</label>
					<textarea class="form-control" id="descricao" name="descricao" rows="5" placeholder="Descriçao da reunião" required></textarea>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
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
				<div class="col-md-3">
					<div class="form-outline">
						<label for="data_reuniao" class="form-label">Data da reunião</label>
						<input id="data_reuniao" name="data_reuniao" class="form-control" type="date" required />
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-outline">
						<label for="hora_reuniao" class="form-label">Horário da reunião</label>
						<input id="hora_reuniao" name="hora_reuniao" class="form-control" type="time" required />
					</div>
				</div>
			</div>
			<br>
			<div style="height: 1px;background-color:grey"></div>
			<br>
			<button class="btn btn-sm btn-cadastrar" type="submit">Cadastrar</button>
			<a class="btn btn-sm btn-voltar" href="{base_url()}reuniao">Voltar</a>
		</form>
	</div>
{/block}