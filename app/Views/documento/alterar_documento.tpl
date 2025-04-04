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
				<div class="row">
					<div class="col-12">
						<label for="texto" class="form-label">Arquivo</label>
						<input class="form-control" type="text" value="{$documento->nome}" disabled/>
					</div>
				</div>
				<div class="row">
					<div class="col-3">
						<label for="vinculo_documento" class="form-label">Vinculo</label>
						<select class="form-control" name="vinculo_documento" id="vinculo_documento" required>
							<option value="">Selecione</option>
							{foreach from=$vinculos_documento item=vinculo_documento}
								<option value="{$vinculo_documento}"
									{if $smarty.post}
										{if $smarty.post.vinculo_documento == $vinculo_documento}
											selected
										{/if}
									{else if $documento->vinculo == $vinculo_documento}
										selected
									{/if}
								>
									{$vinculo_documento|ucfirst}
								</option>
							{/foreach}
						</select>
					</div>
					<div class="col-3">
						<label for="grupo_id" class="form-label">Grupo Responsável</label>
						<select class="form-control" name="grupo_id" id="grupo_id" required>
							<option value="">Selecione</option>
							{foreach from=$grupos item=grupo}
								<option value="{$grupo->id}"
									{if $smarty.post}
										{if $smarty.post.grupo_id == $grupo->id}
											selected
										{/if}
									{else if $documento->vinculo == 'grupo' && $documento->referencia_id == $grupo->id}
										selected
									{/if}
								>
									{$grupo->nome|ucfirst}
								</option>
							{/foreach}
						</select>
					</div>
					<div class="col-3">
						<label for="tipo_documento" class="form-label">Tipo de Documento</label>
						<select class="form-control" name="tipo_documento" id="tipo_documento" required>
							<option value="">Selecione</option>
							{foreach from=$tipos_documento item=tipo_documento}
								<option value="{$tipo_documento}"
									{if $smarty.post}
										{if $smarty.post.tipo_documento == $tipo_documento}
											selected
										{/if}
									{else if $documento->vinculo == 'grupo' && $documento->tipo == $tipo_documento}
										selected
									{/if}
								>
									{$tipo_documento|ucfirst}
								</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
			<br>
			<div style="height: 1px;background-color:grey"></div>
			<br>
			<button class="btn btn-primary btn-sm" type="submit">Alterar</button>
			<a class="btn btn-sm btn-voltar" href="{base_url()}documento">Voltar</a>
		</form>
	</div>
{/block}