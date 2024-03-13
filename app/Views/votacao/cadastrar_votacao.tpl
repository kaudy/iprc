{extends file="../head.tpl"}
{block name=main}
	<script>
		$(document).ready(function() {

		});

	</script>
	<div class="container">
		<div class="title">
			<h2>Cadastrar Votação</h2>
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
					<label for="texto">Descriçao/Texto</label>
					<textarea class="form-control" id="texto" name="texto" rows="3" placeholder="Descriçao/Texto da pergunta da votação" required></textarea>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					<div class="form-outline">
						<label for="qtd_escolhas" class="form-label">Quantidade Escolhas</label>
						<input type="text" class="form-control" id="qtd_escolhas" name="qtd_escolhas" placeholder="" required>
					</div>
				</div>
			</div>
			<br>
			<div style="height: 1px;background-color:grey"></div>
			<br>
			<button class="btn btn-primary btn-sm" type="submit">Cadastrar</button>
			<a class="btn btn-outline-warning btn-sm" href="{base_url()}usuario">Voltar</a>
		</form>
	</div>
{/block}