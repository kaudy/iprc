{extends file="../head.tpl"}
{block name=main}
	<script>
	</script>
	<form class="was-validated" method="post">
		<section class="vh-70 gradient-custom">
			<div class="container py-5 h-700">
				<div class="row d-flex justify-content-center align-items-center h-90">
					<div class="col-12 col-md-8 col-lg-6 col-xl-5">
						<div class="card  text-white" style="border-radius: 1rem;background-color: #2f5b7a;">
							<div class="card-body p-5 text-center">
								<div class="mb-md-4 mt-md-4 pb-3">
									<h2 class="fw-bold mb-2 text-uppercase">Recupar Senha</h2>
									<br>
									<p>
										Ao realizar esse processo a conta será bloqueada até ser feito a ativação e troca da senha.
									</p>
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
										<input type="email" id="email_recuperacao" name="email_recuperacao" class="form-control"
											placeholder="Email Válido" required/>
									</div>
									</p>
									<button class="btn btn-outline-light btn-lg px-5" type="submit">Recuperar Senha</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</form>
{/block}