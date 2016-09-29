<?
if($arResult["ERROR"])
	foreach ($arResult["ERROR"] as $error)
		echo "<span style='color:red;'>".$error."</span>";
?>
<div class="comments-add">
	<h3 class="comments-add__title">написать свой</h3>
	<div class="comments-media__img  hidden-xs">
		<div style="background-image:url('<?=($USER->GetParam("PERSONAL_PHOTO")!='' ? CFILE::GetPath($USER->GetParam("PERSONAL_PHOTO")) : DEFAULT_USERPHOTO)?>');" class="comments-img  crop-image"></div>
	</div>
	<div class="comments-media__body">
		<form method="POST" action="" class="comments-add__form">
			<?=bitrix_sessid_post()?>
			<textarea rows="10" name="comment" class="comments-add__input" id="commentText" placeholder="<?=$arResult["placeholder"]?>" <?=$arResult["data"]?>></textarea>
			<input type="submit" class="comments-add__submit <?=$arResult["submit_class"]?>  [ btn  btn--small  btn--violet  btn--right ] " name="hlblock_submit" value="<?=$arResult["submit_text"]?>" <?=$arResult["data"]?>>
		</form>
	</div>
</div>