{extends file="../head.tpl"}
{block name=main}
	<script>
		$(document).ready(function() {

		});

		// Adiciona elementos no grupo de array
		var gruposSelecionados = [];
		var gruposSelecionadosNomes = [];
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
			$('#votacao_grupos').val(gruposSelecionados);

			if (gruposSelecionados.length > 0) {
				var content = '';
				for (let index = 0; index < gruposSelecionadosNomes.length; index++) {
					content += "<tr>";
					content += "<td>" + gruposSelecionadosNomes[index] +"</td>";
					content += '<td> <a class="btn btn-outline-danger btn-sm" onclick="removeGrupo('+ gruposSelecionados[index] + ');">Remover</a></td>';
					content += "</tr>";
				}
				$('#tabela_listagem_votacao_grupo tbody').empty().append(content);
				$('#tabela_listagem_votacao_grupo').removeAttr("hidden");
				$('#listagem_votacao_grupo').removeAttr("hidden");
			} else {
				$('#tabela_listagem_votacao_grupo tbody').empty();
				$('#tabela_listagem_votacao_grupo').attr("hidden");
				$('#listagem_votacao_grupo').attr("hidden");
			}
		}
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
			<div class="row">
				<div class="col-md-6">
					<div class="form-outline">
						<label for="grupo_id" class="form-label">Grupos</label>
						<div class="row">
							<div class="col">
								<input type="hidden" name="votacao_grupos" id="votacao_grupos" value="[]">
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
			<div class="row" id="listagem_votacao_grupo" hidden>
				<div class="col">
					<table id="tabela_listagem_votacao_grupo" cellspacing="1" class="table table-sm table-responsive table-striped" hidden>
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
			<button class="btn btn-primary btn-sm" type="submit">Cadastrar</button>
			<a class="btn btn-outline-warning btn-sm" href="{base_url()}usuario">Voltar</a>
		</form>
	</div>
{/block}