{extends file="head.tpl"}
{block name=main}
	<div class="container p-2">
		<div class="row m-2">
			{foreach from=$usuario_sessao->modulos item=modulo key=modulo_key}
				{if $modulo_key % 3 == 0}
					<div class="divisor-default">
						<br>
					</div>
				{/if}
				<div class="col-xl-4 col-sm-4 col-12 m-sm-0 m-xl-0 m-1">
					<a href="{url_to("{$modulo->rota}")}" style="text-decoration: none">
						<div class="card col-xl-7 col-sm-12 col-12 rounded p-5 text-white botao-default">
							{if $modulo->rota == 'votacao'}
								<i class="bi-check2-square" style="font-size: 3rem;"></i>
							{elseif $modulo->rota == 'reuniao'}
								<i class="bi-calendar-date" style="font-size: 3rem;"></i>
							{elseif $modulo->rota == 'documento'}
								<i class="bi-file-earmark-text" style="font-size: 3rem;"></i>
							{else}
								<i class="bi-file-earmark-text" style="font-size: 3rem;"></i>
							{/if}
							<h5 class="title" style="font-size: max(1.5em, 12px);font-size-adjust:0.5px;flex-wrap:nowrap;wrap:nowrap";>{$modulo->nome|capitalize}</h4>
						</div>
					</a>
				</div>
			{/foreach}
		</div>
	</div>
{/block}