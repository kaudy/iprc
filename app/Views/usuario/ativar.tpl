{extends file="../head.tpl"}
{block name=main}
	<script>
		(function() {
			'use strict';
			window.addEventListener('load', function() {
				var forms = document.getElementsByClassName('needs-validation');
				// Loop over them and prevent submission
				var validation = Array.prototype.filter.call(forms, function(form) {
					form.addEventListener('submit', function(event) {
						if (validaSenha() === false) {
							event.preventDefault();
							event.stopPropagation();
						}else {
							form.classList.add('was-validated');
						}
					}, false);
				});
			}, false);
		})();

		function validaSenha() {
			var senha = $('#senha').val();
			var confirmarSenha = $('#confirmar_senha').val();

			if(senha === confirmarSenha && senha.length >= 8) {
				$('#confirmar_senha').removeClass('is-invalid');
				$('#confirmar_senha').addClass('is-valid');
				$('#senha').addClass('is-valid');
				return true;
			}else {
				if(senha === confirmarSenha && senha.length <= 8) {
					$('#tooltip_confirmar_senha').text('Senha deve conter no mínimo 8 dígitos');
				}else {
					$('#tooltip_confirmar_senha').text('Confirmação de senha inválida!');
				}
				$('#confirmar_senha').removeClass('is-valid');
				$('#confirmar_senha').addClass('is-invalid');
				$('#senha').removeClass('is-valid');
				return false;
			}
		}
	</script>
	<form class="needs-validation" method="post">
		<section class="vh-70 gradient-custom">
			<div class="container py-5 h-700">
				<div class="row d-flex justify-content-center align-items-center h-90">
					<div class="col-12 col-md-8 col-lg-6 col-xl-5">
						<div class="card  text-white" style="border-radius: 1rem;background-color: #2f5b7a;">
							<div class="card-body p-5 text-center">
								<div class="mb-md-4 mt-md-4 pb-3">
									<h2 class="fw-bold mb-2 text-uppercase">Alteração de senha</h2>
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
									{csrf_field()}
									<div class="form-outline form-white mb-4">
										<label class="form-label" for="senha">Nova Senha</label>
										<input type="password" id="senha" name="senha" class="form-control"
											placeholder="Digite a nova senha" onchange="validaSenha();" onkeyup="validaSenha();" required/>
									</div>
									<div class="form-outline form-white mb-4 has-validation position-relative">
										<label class="form-label" for="confirmar_senha">Confirmação da Senha</label>
										<input type="password" id="confirmar_senha" name="confirmar_senha"
											class="form-control" placeholder="Digite a confirmação da senha" onchange="validaSenha();" onkeyup="validaSenha();" required/>
										<div class="invalid-tooltip" id="tooltip_confirmar_senha">
										</div>
									</div>
									</p>
									<br>
									<button class="btn btn-outline-light btn-lg px-5" type="submit">Alterar Senha</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</form>
{/block}