<?php
/* Smarty version 4.3.4, created on 2024-01-24 20:35:50
  from '/application/iprc/app/Views/usuario/listar.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.4',
  'unifunc' => 'content_65b174a6797fb5_42674250',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8e50687aa5a81654fcac6b7f7d2f6a832e294cc1' => 
    array (
      0 => '/application/iprc/app/Views/usuario/listar.tpl',
      1 => 1705584513,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65b174a6797fb5_42674250 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>

<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_10637450165b174a6782ef3_63213600', 'main');
$_smarty_tpl->inheritance->endChild($_smarty_tpl, "../head.tpl");
}
/* {block 'main'} */
class Block_10637450165b174a6782ef3_63213600 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'main' => 
  array (
    0 => 'Block_10637450165b174a6782ef3_63213600',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/application/iprc/vendor/smarty/smarty/libs/plugins/modifier.count.php','function'=>'smarty_modifier_count',),1=>array('file'=>'/application/iprc/vendor/smarty/smarty/libs/plugins/function.cycle.php','function'=>'smarty_function_cycle',),));
?>

	<div class="container">
		<div class="title">
			<h2>Usuários</h2>
		</div>
		<?php if ($_smarty_tpl->tpl_vars['data']->value['msg'] != null && $_smarty_tpl->tpl_vars['data']->value['msg'] != '') {?>
			<div class="alert alert-<?php echo $_smarty_tpl->tpl_vars['data']->value['msg_type'];?>
" role="alert">
				<?php echo $_smarty_tpl->tpl_vars['data']->value['msg'];?>

				<?php if (smarty_modifier_count($_smarty_tpl->tpl_vars['data']->value['errors']) > 0) {?>
					<ul>
						<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['errors'], 'error');
$_smarty_tpl->tpl_vars['error']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['error']->value) {
$_smarty_tpl->tpl_vars['error']->do_else = false;
?>
							<li><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
</li>
						<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
					</ul>
				<?php }?>
			</div>
		<?php }?>
		<div class="row">
			<table cellspacing="1" summary="Listagem de todos usuários" class="table">
				<thead>
					<tr>
						<th scope="col">
							#
						</th>
						<th scope="col">
							Usuário
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
					<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['usuarios']->value, 'registro');
$_smarty_tpl->tpl_vars['registro']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['registro']->value) {
$_smarty_tpl->tpl_vars['registro']->do_else = false;
?>
						<tr class="<?php echo smarty_function_cycle(array('values'=>'odd,even'),$_smarty_tpl);?>
">
							<td data-title="Usuário">
								<?php echo $_smarty_tpl->tpl_vars['registro']->value->id;?>

							</td>
							<td data-title="Usuário">
								<?php echo $_smarty_tpl->tpl_vars['registro']->value->nome;?>

							</td>
							<td data-title="Status">
								<?php echo $_smarty_tpl->tpl_vars['registro']->value->status;?>

							</td>
							<td data-title="Ações">
								<a class="btn btn-outline-primary btn-sm"
									href="<?php echo url_to('usuario_alterar',$_smarty_tpl->tpl_vars['registro']->value->id);?>
">Alterar</a>
								<a class="btn btn-outline-secondary btn-sm"
									href="<?php echo url_to('usuario_visualizar',$_smarty_tpl->tpl_vars['registro']->value->id);?>
">Visualizar</a>
							</td>
						</tr>
					<?php
}
if ($_smarty_tpl->tpl_vars['registro']->do_else) {
?>
						<tr>
							<td colspan="3" class="null">
								<p>Registro indisponível</p>
							</td>
						</tr>
					<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
				</tbody>
			</table>
		</div>
	</div>
<?php
}
}
/* {/block 'main'} */
}
