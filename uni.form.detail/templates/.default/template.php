<?php
/**
 * Date: 23.12.15
 * Time: 16:20
 */
//

?>
<div class="bride__form">
	<div class="silverline hidden-xs"></div>
	<div class="paperclip hidden-xs"><img src="/projects/sp/MBand/assets/images/bride/paperclip.png"></div>
	<div class="flower hidden-xs"><img src="/projects/sp/MBand/assets/images/bride/flower.png"></div>
	<div class="bride__heading  text--red  text--noshadow bride__heading--left  bride__container  bride__heading--big">Анкета участницы</div>
	<div class="bride__container">
<? $secret = ''; ?>
<?foreach ($arResult['CLEAR_ANSWERS'] as $key => $arAnswer):?>
	<? if ($arAnswer['COMMENTS'] != 'secret'): ?>
<!--			-->
			<? if ($arAnswer['FIELD_TYPE'] == 'image'): ?>
				<?
				$resize = CFile::ResizeImageGet($arAnswer['USER_FILE_ID'], Array("width" => 184, "height" => 198));
				?>
				<div class="answer__image" style="background-image: url('<?= $resize['src']?>');"></div>
			<?endif;?>

			<? if ($arAnswer['FIELD_TYPE'] == 'dropdown'): ?>
			<div class="answer">
				<div class="answer__wrap"><span class="answer__label"><?=$arAnswer['TITLE']?></span><!--
				 --><span class="answer__value"><?=$arAnswer['ANSWER_TEXT']?></span>
				</div>
			</div>
			<? endif; ?>
					
			<? if ($arAnswer['FIELD_TYPE'] == 'text'): ?>
			<?
			if ($arAnswer['COMMENTS'] == 'short') {
				$class = ' answer--short';
				$class .= ' n'.substr($arAnswer['C_SORT'],-1,1);
			} else {
				$class = '';
			}
			?>
			<div class="answer<?=$class?>">
				<div class="answer__wrap"><span class="answer__label"><?=$arAnswer['TITLE']?></span><!--
				 --><span class="answer__value"><?=$arAnswer['USER_TEXT']?></span>
				</div>
			</div>
			<? endif; ?>


			<? if ($arAnswer['FIELD_TYPE'] == 'textarea'): ?>
			<div class="answer">
				<div class="answer__wrap"><span class="answer__label"><?=$arAnswer['TITLE']?></span><!--
				 --><div class="answer__value"><?=$arAnswer['USER_TEXT']?></div>
				</div>
			</div>
			<? endif; ?>
			
	<? else: ?>
		<?ob_start();?>
		<? if ($arAnswer['FIELD_TYPE'] == 'dropdown'): ?>
			<div class="answer">
				<div class="answer__wrap"><span class="answer__label"><?=$arAnswer['TITLE']?></span><!--
				 --><span class="answer__value"><?=$arAnswer['ANSWER_TEXT']?></span>
				</div>
			</div>
		<? else: ?>
		<div class="answer">
			<div class="answer__wrap"><span class="answer__label"><?=$arAnswer['TITLE']?></span><!--
				 --><span class="answer__value"><?=$arAnswer['USER_TEXT']?></span>
			</div>
		</div><?
		endif;
		$secret .= ob_get_contents();
		ob_end_clean();?>

	<? endif; ?>
<?endforeach;?>
<!-- -->
    </div>

</div>
<div class="result__footer">
	<?
	if ($_GET['h'] == '4b7a4b78a429a65ed857423ccd1d5041') {
		echo $secret;
	}
	?>
	<a class="btn" href="<?=$_SERVER['SHORT_TRUE_URL']?>?get=list">закрыть</a>
</div>