{extends file="../head.tpl"}
{block name=main}
	<script>
		$(document).ready(function() {

		});

	</script>
	<div class="container">
		<div class="title">
			<h2>Reunião - Justificativa</h2>
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
				<label for="titulo">
					<span class="nameField">
						Titulo:
					</span>
					<p>
						<strong class="infoTxt">{$reuniao->titulo}</strong>
					</p>
				</label>
			</div>
			<div class="col-12">
				<label for="descricao">
					<span class="nameField">
						Texto/Descrição:
					</span>
					<p>
						<strong class="infoTxt">{$reuniao->descricao}</strong>
					</p>
				</label>
			</div>
			<div class="row">
				<div class="col-md-3">
					<div class="outline">
						<label for="qtd_escolhas" class="label">
						Grupo Proprietário
						</label>
						<p>
							<strong class="infoTxt">{$grupo_proprietario->nome}</strong>
						</p>
					</div>
				</div>
				<div class="col-md-3">
					<div class="outline">
						<label for="status" class="label">Data e Hora</label>
						<p>
							<strong class="infoTxt">{$reuniao->data_reuniao|DataHoraConvertBrString}</strong>
						</p>
					</div>
				</div>
				<div class="col-md-3">
					<div class="outline">
						<label for="status" class="label">Status</label>
						<p>
							<strong class="infoTxt">{$reuniao->status_id|statusNome}</strong>
						</p>
					</div>
				</div>
			</div>
		</div>
		<div style="height: 1px;background-color:grey"></div>
		<br>
		<form class="was-validated" method="post" onsubmit="">
			{csrf_field()}
			<div class="row">
				<div class="col-12">
					<label for="texto">Justificativa</label>
					<textarea class="form-control" id="justificativa" name="justificativa" rows="5" placeholder="Justificativa de ausência da reunião" required></textarea>
				</div>
			</div>
			<br>
			<div style="height: 1px;background-color:grey"></div>
			<br>
			<button class="btn btn-sm btn-cadastrar" type="submit">Cadastrar</button>
			<a class="btn btn-sm btn-voltar" href="{previous_url()}">Voltar</a>
		</form>
	</div>
{/block}