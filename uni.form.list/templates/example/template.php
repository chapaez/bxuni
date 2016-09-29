<?php
/**
 * Date: 23.12.15
 * Time: 16:20
 */
//
?>
<? if (count($arResult['CLEAR_ANSWERS']) <= 0): ?>
	<?= htmlspecialchars_decode($arResult['BLANK_RESULT_TEXT'])?>
<? else: ?>
<div class="items" data-page="<?=$arResult['CUR_PAGE']?>" data-pages="<?=$arResult['PAGES']?>">
<!--
<? foreach ($arResult['CLEAR_ANSWERS'] as $res_id => $answers): ?>
	--><div class="item"><a href="<?=$arResult['URL']?>?anketa_id=<?=$res_id?>" class="item__link">
		<? $text = ''; ?>
		<? foreach ($answers as $key => $arAnswer): ?>
			<? if ($arAnswer['COMMENTS'] == 'photo'): ?>
				<?
				$resize = CFile::ResizeImageGet($arAnswer['USER_FILE_ID'], Array("width" => 242, "height" => 200));
				?>
				<div class="box  item__image" style="background-image: url('<?= $resize['src']?>');">
				</div>
			<? endif; ?>
			<? if ($arAnswer['COMMENTS'] == 'name'): ?>
				<? ob_start(); ?>
					<div class="item__name"><?=$arAnswer['USER_TEXT']?></div>
				<? $text .= ob_get_contents(); ob_end_clean(); ?>
			<? endif; ?>
			<? if ($arAnswer['COMMENTS'] == 'city'): ?>
				<? ob_start(); ?>
				<div class="item__city"><?=$arAnswer['USER_TEXT']?></div>
				<? $text .= ob_get_contents(); ob_end_clean(); ?>	
			<? endif; ?>
		<? endforeach; ?>
		<div class="box  item__data"><?=$text?></div>
	</a></div><!--
<? endforeach; ?>
<!-- -->
<? if ($arResult['CUR_PAGE'] < $arResult['PAGES']): ?>
		<a class="btn  btn--transparent" onclick="javascript:getPage($('.items').data('page')+1)">ЕЩЁ</a>
<? endif; ?>
</div>
<? endif; ?>