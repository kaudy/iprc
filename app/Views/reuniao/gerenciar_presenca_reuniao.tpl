{extends file="../head.tpl"}
{block name=main}
	<script>
		$(document).ready(function() {
			verificaBarraAcao();
		});

		function submit(elemento, acao) {
			//elemento.preventDefault();
			// elemento.setAttribute('disabled', true);
			// console.log(acao);
			// $('#acao').val(acao);
			//$( "#formulario" ).submit();
			//$( "#formulario" ).trigger( "submit" );
		}

		document.addEventListener('DOMContentLoaded', function() {
			const barraRodape = document.querySelector('#barra_acao_rodape');

			// Executa a função sempre que a janela for rolada
			window.addEventListener('scroll', function() {
				verificaBarraAcao();
			});
			window.addEventListener('resize', function() {
				verificaBarraAcao();
			});
		});

		function verificaBarraAcao() {
			const innerHeight = window.innerHeight;
			const scrollHeight = document.documentElement.scrollHeight;

			// Verifica se a altura do conteúdo é maior que a altura visível
			if (scrollHeight > (innerHeight + 80)) {
				// A página tem barra de rolagem
				document.querySelector('#barra_acao_rodape').classList.add('barra-acao-rodape-fixa');
				document.querySelector('#corpo').classList.add('corpo-rodape-fixo');
			} else {
				// A página não tem barra de rolagem
				document.querySelector('#barra_acao_rodape').classList.remove('barra-acao-rodape-fixa');
				document.querySelector('#corpo').classList.remove('corpo-rodape-fixo');
			}
		}

		// Justificar múltiplos
		function justificarMultiplos(acao) {
			event.preventDefault();
			const checkboxes = document.querySelectorAll('.justificativa-multiplas-selecoes:checked');
			if (checkboxes.length === 0) {
				alert('Selecione ao menos um participante para justificar.');
				return;
			}
			$('#acao').val(acao);
			const idsSelecionados = Array.from(checkboxes).map(cb => cb.value);
			console.log(idsSelecionados);
			$( "#formulario" ).submit();
		}

		// Marcar múltiplas presenças
		function presenteMultiplos(acao) {
			event.preventDefault();
			const checkboxes = document.querySelectorAll('.justificativa-multiplas-selecoes:checked');
			if (checkboxes.length === 0) {
				alert('Selecione ao menos um participante para marcar como presente.');
				return;
			}
			$('#acao').val(acao);
			const idsSelecionados = Array.from(checkboxes).map(cb => cb.value);
			console.log(idsSelecionados);
			$( "#formulario" ).submit();
		}

		// Marcar múltiplas ausências
		function ausenteMultiplos(acao) {
			event.preventDefault();
			const checkboxes = document.querySelectorAll('.justificativa-multiplas-selecoes:checked');
			if (checkboxes.length === 0) {
				alert('Selecione ao menos um participante para marcar como ausente.');
				return;
			}
			$('#acao').val(acao);
			const idsSelecionados = Array.from(checkboxes).map(cb => cb.value);
			console.log(idsSelecionados);
			$( "#formulario" ).submit();
		}

	</script>
	<div class="container">
		<form method="post" id="formulario" enctype="multipart/form-data">
			<input type="hidden" name="acao" id="acao" value="" required>
			{csrf_field()}
			<div class="row">
				<div class="col-md-9">
					<h2>Reunião #{$reuniao->id} - Gerenciar Presenças</h2>
				</div>
			</div>
			<br>
			{if $data['msg'] != null && $data['msg'] != ''}
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
			<br>
			<div class="row">
				<div class="col">
					<div class="form-outline">
						<div class="row">
							<div class="col-md-3">
								<label for="titulo" class="label">Nome</label>
								<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome"
									value="{if $smarty.post}{$smarty.post.nome}{/if}">
							</div>
							<div class="col-md-3">
								<label for="titulo" class="label">Status</label>
								<select class="form-control" name="tipo_status_id" id="tipo_status_id">
									<option value="">Selecione</option>
									{foreach from=$tipos_status item=tipo_status}
										<option value="{$tipo_status->id}"
											{if $smarty.post}{if $smarty.post.tipo_status_id == $tipo_status->id}selected{/if}{/if}>
											{$tipo_status->nome|ucfirst}
										</option>
									{/foreach}
								</select>
							</div>
							<div class="col-md-3">
								<label for="grupo_id" class="label">Grupo</label>
								<select class="form-control" name="grupo_id" id="grupo_id">
									<option value="">Selecione</option>
									{foreach from=$grupos item=grupo}
										<option value="{$grupo->id}" {if $smarty.post}{if $smarty.post.grupo_id == $grupo->id}selected{/if}{/if}>
										{$grupo->nome|ucfirst}
									</option>
									{/foreach}
								</select>
							</div>
							<div class="col-md-2 div-pesquisar">
								<button class="btn btn-primary btn-sm btn-pesquisar" type="submit">Pesquisar</button>
							</div>
							<div class="col-md-1 div-pesquisar">
								<button class="btn btn-sm bi btn-filtro bi-funnel" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltros"  data-bs-placement="top" title="Mais filtros" aria-expanded="false" aria-controls="collapseExample"></button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<br>
			<div style="height: 1px;background-color:grey"></div>
			<br>
			<div class="row">
				<table cellspacing="1" summary="Listagem de presenças" class="table table-sm table-responsive table-striped">
					<thead  class="thead-light">
						<tr>
							<th scope="col">
								#
							</th>
							<th scope="col">
								Nome
							</th>
							<th scope="col">
								Justificativa
							</th>
							<th scope="col">
								Status
							</th>
							<th scope="col">
								Ações
							</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$participantes_reuniao item=registro}
							<tr>
								<td data-title="ID" class="align-middle">
									{$registro@iteration}
								</td>
								<td data-title="Usuário" class="align-left">
									{$registro->pessoa_nome}
								</td>
								<td data-title="Justificativa" class="align-left">
									{$registro->justificativa|textoEncurtado:50}
								</td>
								<td data-title="Status" class="align-middle">
									<i class="bi bi-circle-fill {if $registro->presenca_status_nome}{$registro->presenca_status_nome}{/if}"></i>
									{if $registro->presenca_status_nome}{$registro->presenca_status_nome|capitalize}{/if}
								</td>
								<td data-title="Ações" class="align-middle">
									<input class="form-check-input justificativa-multiplas-selecoes" type="checkbox" name="selecao_multiplas[]" id="check_{$registro->id}" value="{$registro->id}">
									<a class="btn btn-sm bi btn-justificar bi-pencil-square" href="{url_to('reuniao_presenca_justificar', $reuniao->id, $registro->id, 'gerenciar')}" title="Justificar"></a>
									<a class="btn btn-sm bi btn-justificar-presente bi-check-square" href="{url_to('reuniao_presenca_confirmar', $reuniao->id, $registro->id, 'presente')}" title="Presente"></a>
									<a class="btn btn-sm bi btn-justificar-ausente bi-x-square" href="{url_to('reuniao_presenca_confirmar', $reuniao->id, $registro->id, 'ausente')}" title="Ausente"></a>
								</td>
							</tr>
						{foreachelse}
							<tr>
								<td colspan="99" class="null">
									<p>Registro indisponível</p>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
			<div id="barra_acao_rodape">
				<div class="row">
					<div class="row">
						<div class="col-md-1">
							<p>
								<strong class="infoTxt">Ações</strong>
							</p>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<a class="btn btn-sm btn-voltar" href="{base_url()}reuniao">Voltar</a>
							<button class="btn btn-sm btn-justificar-multiplos" onclick="justificarMultiplos('justificar_multiplos')">Justificar Múltiplos</button>
							<button class="btn btn-sm btn-presente-multiplos" onclick="presenteMultiplos('presente_multiplos')">Múltiplos Presentes</button>
							<button class="btn btn-sm btn-ausente-multiplos" onclick="ausenteMultiplos('ausente_multiplos')">Múltiplos Ausentes</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div id="corpo" class="corpo-rodape-fixo container">
	</div>
{/block}