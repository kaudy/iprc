<!DOCTYPE html>
<html lang="pt-BR">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/jquery-mask-plugin@1.14.16/dist/jquery.mask.min.js"></script>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<link href="./css/default.css" rel="stylesheet">
	<title>{if $usuario_sessao}{$usuario_sessao->titulo}-{$usuario_sessao->subtitulo}{else}iPRC{/if}</title>
</head>

<body class="p-3 m-0 border-0 bd-example m-0 border-0">
	<div>
		<nav class="navbar fixed-top navbar-expand-lg nav-color">
			<div class="container-fluid">
				<a class="navbar-brand p-1" style="color:white;" href="{base_url()}">iPRC</a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll"
					aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation" style="background-color:white; color:white;">
					<span class="navbar-toggler-icon white"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarScroll">
					<ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
						{foreach from=$usuario_sessao->modulos item=modulo}
							{if $modulo->filhos|count > 0}
								<li class="nav-item dropdown" style="color:white;">
									<a class="nav-link dropdown-toggle" href="{base_url()}{$modulo->rota}" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color:white;" >
										{$modulo->nome}
									</a>
									<ul class="dropdown-menu" style="color:white;">
										<li><a class="dropdown-item" href="{base_url()}{$modulo->rota}">{$modulo->nome}</a></li>
										<li>
											<hr class="dropdown-divider">
										</li>
										{foreach from=$modulo->filhos item=filho}
											<li><a class="dropdown-item" href="{base_url()}{$filho->rota}">{$filho->nome}</a></li>
										{/foreach}
									</ul>
								</li>
							{else}
								<li class="nav-item">
									<a class="nav-link active"  style="color:white;" href="{base_url()}{$modulo->rota}">{$modulo->nome}</a>
								</li>
							{/if}
						{/foreach}

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
					{if $usuario_sessao && $usuario_sessao->logado}
					<a class="btn btn-outline-light m-1" type="submit" href="{base_url()}logout">Sair</a>
					{else}
					<a class="btn btn-outline-light m-1" type="submit" href="{base_url()}login">Entrar</a>
					{/if}
				</div>
			</div>
		</nav>
	</div>
	{block name=main}{/block}
</body>

</html>