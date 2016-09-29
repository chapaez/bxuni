<?
if($USER->IsAuthorized()){
	$arResult["placeholder"]    = 'Введите свой коментарий...';
	$arResult["submit_text"]    = 'комментировать';
	$arResult["submit_class"]   = 'send-comment';
	$arResult["data"] = '';
}else {
	$arResult["placeholder"]    = 'Авторизуйтесь, чтобы писать комментарии';
	$arResult["submit_text"]    = 'комментировать';
	$arResult["submit_class"]   = ' authorize-comment ';
	$arResult["data"] = ' data-target="#authModal" data-toggle="modal" ';
}
?>