{extends file="head.tpl"}
{block name=main}
	<div class="container p-2">
		<div class="row m-2">
			<div class="col-xl-4 col-sm-4 col-12 m-sm-0 m-xl-0 m-1">
				<a href="{url_to('reuniao')}" style="text-decoration: none">
					<div class="card col-xl-7 col-sm-12 col-12 rounded p-5 text-white" style="background-color: #00448b;color:white;">
						<i class="bi-calendar-date" style="font-size: 3rem;"></i>
						<h5 class="title" style="font-size: max(2em, 12px);font-size-adjust:0.5;";>Reuniões</h4>
					</div>
				</a>
			</div>
			<div class="col-xl-4 col-sm-4 col-12 m-sm-0 m-xl-0 m-1">
				<a href="{url_to('votacao')}" style="text-decoration: none">
					<div class="card card col-xl-7 col-sm-12 col-12 rounded p-5 text-white" style="background-color: #00448b;color:white;">
						<i class="bi-check2-square" style="font-size: 3rem;"></i>
						<h5 class="title" style="font-size: max(2em, 12px);font-size-adjust:0.5;">Votações</h4>
					</div>
				</a>
			</div>
			<div class="col-xl-4 col-sm-4 col-12 m-sm-0 m-xl-0 m-1">
				<a href="{url_to('documento')}" style="text-decoration: none">
					<div class="card card col-xl-7 col-sm-12 col-12 rounded p-5 text-white" style="background-color: #00448b;color:white;">
						<i class="bi-file-earmark-text" style="font-size: 3rem;"></i>
						<h5 class="title" style="font-size: max(2em, 12px);font-size-adjust:0.5;">Docs</h4>
					</div>
				</a>
			</div>
		</div>
	</div>
{/block}