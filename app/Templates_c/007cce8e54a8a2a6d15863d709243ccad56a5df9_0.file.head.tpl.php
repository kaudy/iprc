<?php
/* Smarty version 4.3.4, created on 2024-01-24 20:35:50
  from '/application/iprc/app/Views/head.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.4',
  'unifunc' => 'content_65b174a67b0df6_91160610',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '007cce8e54a8a2a6d15863d709243ccad56a5df9' => 
    array (
      0 => '/application/iprc/app/Views/head.tpl',
      1 => 1705584513,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65b174a67b0df6_91160610 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/application/iprc/vendor/smarty/smarty/libs/plugins/modifier.count.php','function'=>'smarty_modifier_count',),));
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php echo '<script'; ?>
 src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="https://cdn.jsdelivr.net/npm/jquery-mask-plugin@1.14.16/dist/jquery.mask.min.js"><?php echo '</script'; ?>
>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
	<?php echo '<script'; ?>
 src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"><?php echo '</script'; ?>
>
	<title><?php if ($_smarty_tpl->tpl_vars['usuario_sessao']->value) {
echo $_smarty_tpl->tpl_vars['usuario_sessao']->value->titulo;?>
-<?php echo $_smarty_tpl->tpl_vars['usuario_sessao']->value->subtitulo;
} else { ?>iPRC<?php }?></title>
</head>

<body class="p-3 m-0 border-0 bd-example m-0 border-0">
	<div>
		<nav class="navbar fixed-top navbar-expand-lg" style="background-color: #2f5b7a;color:white;">
			<div class="container-fluid">
				<a class="navbar-brand" style="color:white;" href="<?php echo base_url();?>
">iPRC</a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll"
					aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation" style="background-color:white; color:white;">
					<span class="navbar-toggler-icon white" style="background-color:white; color:white;"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarScroll">
					<ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
						<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['usuario_sessao']->value->modulos, 'modulo');
$_smarty_tpl->tpl_vars['modulo']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['modulo']->value) {
$_smarty_tpl->tpl_vars['modulo']->do_else = false;
?>
							<?php if (smarty_modifier_count($_smarty_tpl->tpl_vars['modulo']->value->filhos) > 0) {?>
								<li class="nav-item dropdown" style="color:white;">
									<a class="nav-link dropdown-toggle" href="<?php echo base_url();
echo $_smarty_tpl->tpl_vars['modulo']->value->rota;?>
" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color:white;" >
										<?php echo $_smarty_tpl->tpl_vars['modulo']->value->nome;?>

									</a>
									<ul class="dropdown-menu" style="color:white;">
										<li><a class="dropdown-item" href="<?php echo base_url();
echo $_smarty_tpl->tpl_vars['modulo']->value->rota;?>
"><?php echo $_smarty_tpl->tpl_vars['modulo']->value->nome;?>
</a></li>
										<li>
											<hr class="dropdown-divider">
										</li>
										<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['modulo']->value->filhos, 'filho');
$_smarty_tpl->tpl_vars['filho']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['filho']->value) {
$_smarty_tpl->tpl_vars['filho']->do_else = false;
?>
											<li><a class="dropdown-item" href="<?php echo base_url();
echo $_smarty_tpl->tpl_vars['filho']->value->rota;?>
"><?php echo $_smarty_tpl->tpl_vars['filho']->value->nome;?>
</a></li>
										<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
									</ul>
								</li>
							<?php } else { ?>
								<li class="nav-item">
									<a class="nav-link active"  style="color:white;" href="<?php echo base_url();
echo $_smarty_tpl->tpl_vars['modulo']->value->rota;?>
"><?php echo $_smarty_tpl->tpl_vars['modulo']->value->nome;?>
</a>
								</li>
							<?php }?>
						<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

						<!--
						<li class="nav-item">
							<a class="nav-link active" aria-current="page" href="/">Home</a>
						</li>

						<li class="nav-item">
							<a class="nav-link active" href="usuario">Usuários</a>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
								aria-expanded="false">
								Deliberativo
							</a>
							<ul class="dropdown-menu">
								<li><a class="dropdown-item" href="#">Atas</a></li>
								<li><a class="dropdown-item" href="#">Conselheiros</a></li>
								<li><a class="dropdown-item" href="#">Documentos</a></li>
								<li><a class="dropdown-item" href="#">Relatórios</a></li>
								<li>
									<hr class="dropdown-divider">
								</li>
								<li><a class="dropdown-item" href="#">Something else here</a></li>
							</ul>
						</li>
						<li class="nav-item">
							<a class="nav-link disabled" aria-disabled="true">Link</a>
						</li>
						!-->
					</ul>

					<!--
					<form class="d-flex" role="search">
						<input class="form-control me-2" type="search" placeholder="Procurar" aria-label="Procurar">
						<button class="btn btn-outline-light" type="submit">Procurar</button>
					</form>
					!-->
					<?php if ($_smarty_tpl->tpl_vars['usuario_sessao']->value && $_smarty_tpl->tpl_vars['usuario_sessao']->value->logado) {?>
					<a class="btn btn-outline-light" type="submit" href="<?php echo base_url();?>
logout">Sair</a>
					<?php } else { ?>
					<a class="btn btn-outline-light" type="submit" href="<?php echo base_url();?>
login">Entrar</a>
					<?php }?>
				</div>
			</div>
		</nav>
	</div>
	<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_182706916565b174a67b0071_82793480', 'main');
?>

</body>

</html><?php }
/* {block 'main'} */
class Block_182706916565b174a67b0071_82793480 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'main' => 
  array (
    0 => 'Block_182706916565b174a67b0071_82793480',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block 'main'} */
}
