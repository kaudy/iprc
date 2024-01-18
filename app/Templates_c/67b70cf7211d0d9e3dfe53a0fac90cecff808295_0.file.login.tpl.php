<?php
/* Smarty version 4.3.4, created on 2024-01-18 14:02:46
  from '/application/iprc/app/Views/usuario/login.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.4',
  'unifunc' => 'content_65a92f8678e9e9_06441419',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '67b70cf7211d0d9e3dfe53a0fac90cecff808295' => 
    array (
      0 => '/application/iprc/app/Views/usuario/login.tpl',
      1 => 1705584513,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65a92f8678e9e9_06441419 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>

<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_159298105865a92f867863c4_33252534', 'main');
$_smarty_tpl->inheritance->endChild($_smarty_tpl, "../head.tpl");
}
/* {block 'main'} */
class Block_159298105865a92f867863c4_33252534 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'main' => 
  array (
    0 => 'Block_159298105865a92f867863c4_33252534',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>


	<form method="post">
		<section class="vh-70 gradient-custom">
			<div class="container py-5 h-700">
				<div class="row d-flex justify-content-center align-items-center h-90">
					<div class="col-12 col-md-8 col-lg-6 col-xl-5">
						<div class="card  text-white" style="border-radius: 1rem;background-color: #2f5b7a;">
							<div class="card-body p-5 text-center">
								<div class="mb-md-4 mt-md-4 pb-3">
									<h2 class="fw-bold mb-2 text-uppercase">Login</h2>
									<?php if ($_smarty_tpl->tpl_vars['data']->value['msg'] != null) {?>
										<p class="text-50 mb-3 alert-danger" style="color: red;">
											<?php echo $_smarty_tpl->tpl_vars['data']->value['msg'];?>

										</p>
									<?php }?>
									<?php echo csrf_field();?>

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
									<p class="small mb-3 pb-lg-2"><a class="text-white-50" href="#!">Esqueceu a senha?</a>
									</p>
									<button class="btn btn-outline-light btn-lg px-5" type="submit">Login</button>
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
<?php
}
}
/* {/block 'main'} */
}
