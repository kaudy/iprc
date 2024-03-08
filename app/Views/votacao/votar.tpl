{extends file="../head.tpl"}
{block name=main}
	<script>
	$(document).ready(function() {
	});

	function submit(elemento, acao) {
		//elemento.preventDefault();
		// elemento.setAttribute('disabled', true);
		// console.log(acao);
		// $('#acao').val(acao);
		//$( "#formulario" ).submit();
		//$( "#formulario" ).trigger( "submit" );
	}
	</script>
	<div class="container">
		<form method="post" id="formulario">
			<input type="hidden" name="acao" id="acao" value="" required>
			{csrf_field()}
			<div class="title">
				<h2>Votar - {$votacao->titulo}</h2>
			</div>
			<div style="height: 1px;background-color:grey"></div>
			<br>
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
							Descrição:
						</span>
						<p>
							<strong class="infoTxt">{$votacao->texto}</strong>
						</p>
					</label>
				</div>
				<div class="row">
					<div class="col-md-3">
						<div class="outline">
							<label for="qtd_escolhas" class="label">
							Qtde. Escolhas
							</label>
							<p>
								<strong class="infoTxt">{$votacao->qtd_escolhas}</strong>
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
					<div class="col">
						<table class="table table-responsive table-striped">
							<tbody>
								<tr>
									<th scope="col" colspan="2">
										Selecione {if $votacao->qtd_escolhas == 1}uma das{else}as{/if} opções a baixo:
									</th>
								</tr>
								{foreach from=$votacao_opcoes item=opcao}
									<tr>
										<td scope="row">
											{$opcao->titulo}
										</td>
										<td>
											{if $votacao->qtd_escolhas == 1}
												<input class="form-check-input" type="radio" name="voto" id="voto" value="{$opcao->id}" required>
											{else}
											{/if}
										</td>
									</tr>
								{/foreach}
							</tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						{if $votacao->status == 1}
							<button class="btn btn-primary" type="submit">Votar</button>
						{/if}
					</div>
				</div>
			</form>
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
						<a class="btn btn-outline-warning" href="{url_to('votacao')}">Voltar</a>
					</div>
				</div>
			</div>
		</form>
	</div>
{/block}