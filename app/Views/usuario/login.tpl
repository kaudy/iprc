{extends file="../head.tpl"}
{block name=main}

	<form method="post">
		<section class="vh-70 gradient-custom">
			<div class="container py-5 h-700">
				<div class="row d-flex justify-content-center align-items-center h-90">
					<div class="col-12 col-md-8 col-lg-6 col-xl-5">
						<div class="card text-white login-card">
							<div class="card-body p-5 text-center">
								<div class="mb-md-4 mt-md-4 pb-3">
									<h2 class="fw-bold mb-2 text-uppercase">Login</h2>
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
										<input type="email" id="email" name="email" class="form-control form-control"
											placeholder="Email" />
										<label class="form-label" for="email">Email</label>
									</div>
									<div class="form-outline form-white mb-4">
										<input type="password" id="senha" name="senha" class="form-control form-control"
											placeholder="Senha" />
										<label class="form-label" for="senha">Senha</label>
									</div>
									<p class="small mb-3 pb-lg-2">

									<a class="text-white-50" href="{url_to('usuario_recuperar_senha')}">Esqueceu a senha?</a>
									</p>
									<button class="btn btn-outline-light btn-lg px-5 btn-login" type="submit">Entrar</button>
								</div>
								<!--
								<div>
									<p class="mb-0">
										É conselheiro e não possui uma conta?
									</p>
									<a href="registrar" class="text-white-50 fw-bold">Criar Conta</a>
								</div>
								!-->
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</form>
{/block}