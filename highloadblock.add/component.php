<?
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
if(!empty($_REQUEST["hlblock_submit"])){
	$arResult=Array();

		
	if(empty($arResult["ERROR"])){
		$data=Array();
		try {
			if (!CModule::IncludeModule('highloadblock'))
				throw new Exception("Ошибка модуля");
			
			if(!check_bitrix_sessid())
				throw new Exception("Ошибка сессии");
			$fields = $GLOBALS['USER_FIELD_MANAGER']->GetUserFields('HLBLOCK_'.$arParams["HLBLOCK_ID"], 0, LANGUAGE_ID);
			foreach ($arParams["FIELDS"] as $uf_field=>$uf_array_params){
				
				//mdump($fields);
				if(empty($uf_array_params["TYPE"]))
					throw new Exception("Неверные входные параметры");
				
				if($fields[$uf_field]['MANDATORY']=='Y' && empty($uf_array_params["VALUE"]))
					throw new Exception("Поле ".$fields[$uf_field]['EDIT_FORM_LABEL'].' не заполнено');
				
				if($uf_array_params["TYPE"]=="string")
					$uf_field_val=htmlspecialchars($uf_array_params["VALUE"]);
				elseif ($uf_array_params["TYPE"]=="integer")
					$uf_field_val=intval($uf_array_params["VALUE"]);
				$data[$uf_field]=$uf_field_val;
			}
			if(empty($data))
				throw new Exception("Нет элемента для добавления");
			
			$hlblock = HL\HighloadBlockTable::getById($arParams["HLBLOCK_ID"])->fetch();
			$entity = HL\HighloadBlockTable::compileEntity($hlblock);
			$entity_data_class = $entity->getDataClass();
			if(empty($entity_data_class))
				throw new Exception("Ошибка HL блока");
			
			$result = $entity_data_class::add($data);
			
			if (!$result->isSuccess()) {
				$err_list = '';
				foreach ($result->getErrors() as $error)
					$err_list .= $error->getMessage() . '; ';
				
				throw new Exception($err_list);
			}
			$new_id = $result->getId();
			global $CACHE_MANAGER;
			$CACHE_MANAGER->ClearByTag("hlblock_".$arParams["HLBLOCK_ID"]);
			if($new_id<=0)
				throw new Exception("Не удалось добавить элемент");
			elseif(!$arParams["NO_REDIRECT"])
				LocalRedirect($_SERVER["TRUE_URL"],false,"302 Found"); //prg pattern
			
				//$arResult["NEW_ELEMENT_ID"]=$new_id;
		}catch (Exception $e){
			$arResult["ERROR"][]=$e->GetMessage();
		}
	}
}
$this->IncludeComponentTemplate();
?>