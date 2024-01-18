<?php
/* Smarty version 4.3.4, created on 2024-01-18 14:19:43
  from '/application/iprc/app/Views/usuario/cadastrar.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.4',
  'unifunc' => 'content_65a9337fdddd83_39600879',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2497cc0bc2de6dac87bdbe595c8c44bbba5d3d28' => 
    array (
      0 => '/application/iprc/app/Views/usuario/cadastrar.tpl',
      1 => 1705584513,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65a9337fdddd83_39600879 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>

<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_86602951165a9337fdbca23_24300540', 'main');
$_smarty_tpl->inheritance->endChild($_smarty_tpl, "../head.tpl");
}
/* {block 'main'} */
class Block_86602951165a9337fdbca23_24300540 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'main' => 
  array (
    0 => 'Block_86602951165a9337fdbca23_24300540',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/application/iprc/vendor/smarty/smarty/libs/plugins/modifier.count.php','function'=>'smarty_modifier_count',),));
?>

	<?php echo '<script'; ?>
>
		$(document).ready(function() {
			verificaTipoDocumento();

			var SPMaskBehavior = function(val) {
					return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
				},
				spOptions = {
					onKeyPress: function(val, e, field, options) {
						field.mask(SPMaskBehavior.apply({}, arguments), options);
					}
				};

			$('#telefone').mask(SPMaskBehavior, spOptions);
		});

		function verificaTipoDocumento() {
			var tipoDocumento = document.getElementById("tipo_documento").value;
			var documento = document.getElementById("documento").value;
			if (tipoDocumento == 'CPF') {
				$('#documento').mask('000.000.000-00');
			} else if (tipoDocumento == 'CNPJ') {
				$('#documento').mask('00.000.000/0000-00');
			} else {
				$('#documento').unmask();
			}
		}
	<?php echo '</script'; ?>
>
	<div class="container">
		<div class="title">
			<h2>Adicionar Usuário</h2>
		</div>
		<?php if ($_smarty_tpl->tpl_vars['data']->value['msg'] != null) {?>
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
		<form class="was-validated" method="post" onsubmit="">
			<?php echo csrf_field();?>

			<div class="row">
				<div class="col-12">
					<label for="nome">Nome</label>
					<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome Completo" required>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					<div class="form-outline">
						<label for="tipo_documento" class="form-label">Tipo Documento</label>
						<select class="form-control" name="tipo_documento" id="tipo_documento" required
							onchange="verificaTipoDocumento();">
							<option value="">Selecione</option>
							<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tipos_documento']->value, 'tipo_documento');
$_smarty_tpl->tpl_vars['tipo_documento']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['tipo_documento']->value) {
$_smarty_tpl->tpl_vars['tipo_documento']->do_else = false;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['tipo_documento']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['tipo_documento']->value;?>
</option>
							<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-outline">
						<label for="documento cpf" class="form-label">Nº Documento</label>
						<input type="text" class="form-control" id="documento" name="documento" placeholder="Documento"
							required>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-outline">
						<label for="data_nascimento" class="form-label">Data Nasc.</label>
						<input id="data_nascimento" name="data_nascimento" class="form-control" type="date" required />
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-outline">
						<label for="sexo" class="form-label">Sexo</label>
						<select class="form-control" id="sexo" name="sexo" required>
							<option value="">Selecione</option>
							<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tipos_sexos']->value, 'sexo');
$_smarty_tpl->tpl_vars['sexo']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['sexo']->value) {
$_smarty_tpl->tpl_vars['sexo']->do_else = false;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['sexo']->value;?>
"><?php echo ucfirst($_smarty_tpl->tpl_vars['sexo']->value);?>
</option>
							<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-outline">
						<label for="estado_civil" class="form-label">Estado Civil</label>
						<select class="form-control" name="estado_civil" id="estado_civil" required>
							<option value="">Selecione</option>
							<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['estados_civil']->value, 'estado_civil');
$_smarty_tpl->tpl_vars['estado_civil']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['estado_civil']->value) {
$_smarty_tpl->tpl_vars['estado_civil']->do_else = false;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['estado_civil']->value;?>
"><?php echo ucfirst($_smarty_tpl->tpl_vars['estado_civil']->value);?>
</option>
							<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-outline">
						<label for="telefone" class="form-label">Telefone</label>
						<input type="text" class="form-control" id="telefone" name="telefone"
							placeholder="Preferencial celular" required>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="form-outline">
					<label for="email" class="form-label">Email</label>
					<input type="email" class="form-control" name="email" id="email" placeholder="Email válido" required>
				</div>
			</div>
			<div class="row">
				<div class="form-outline">
					<label for="senha" class="form-label">Senha</label>
					<input type="password" class="form-control" id="senha" name="senha" placeholder="Senha">
					<div class="valid-feedback">
						Se campo senha for mantido vazio será gerado uma senha aleatória para usuario no primeiro login
						trocar.
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-outline">
						<label for="perfil_id" class="form-label">Perfil</label>
						<select class="form-control" name="perfil_id" id="perfil_id" required>
							<option value="">Selecione</option>
							<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['perfis']->value, 'perfil');
$_smarty_tpl->tpl_vars['perfil']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['perfil']->value) {
$_smarty_tpl->tpl_vars['perfil']->do_else = false;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['perfil']->value->id;?>
"><?php echo $_smarty_tpl->tpl_vars['perfil']->value->nome;?>
</option>
							<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
						</select>
					</div>
				</div>
			</div>
			<br>
			<button class="btn btn-primary" type="submit">Cadastrar</button>
			<a class="btn btn-light" href="<?php echo base_url();?>
usuario">Voltar</button>
		</form>
	</div>
<?php
}
}
/* {/block 'main'} */
}
