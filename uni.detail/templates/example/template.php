
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? if ($arResult['ITEM']): ?>
	<div class="active-from"><?=substr($arResult['ITEM']['DATE_ACTIVE_FROM'],0,10)?></div>
	<h2><?=$arResult['ITEM']['NAME']?></h2>
	<div class="detail-pic shadow"><img src="<?=$arResult['ITEM']["DETAIL_PICTURE_SRC"]?>" class="img-responsive"></div>
	
	<div class="news__description">
		<?=$arResult['ITEM']['DETAIL_TEXT']?>
	</div>
	<?$APPLICATION->IncludeFile("/includes/social.php");?>  
<? 
else:
	LocalRedirect("/404/?nonew");
endif;
?>
