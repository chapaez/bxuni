<?if (! defined ( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true) die ();?>
<?
$iblock_code = isset($arParams["IBLOCK_CODE"]) ? $arParams["IBLOCK_CODE"] : false;
if(!$arParams["GROUPS_CAN_WATCH_INACTIVE"])
	$arParams["GROUPS_CAN_WATCH_INACTIVE"]=Array(1,7);
	
if(!$arParams["CACHE_TIME"])
	$arParams["CACHE_TIME"] = 3600;
$id = isset($arParams["ID"]) ? $arParams["ID"] : NULL;
$arr=array_intersect((array) $USER->GetUserGroupArray(),(array) $arParams["GROUPS_CAN_WATCH_INACTIVE"]);
if(!empty($arr))
	$arParams['icanwatch']=true;
else
	$arParams['icanwatch']=false;	
if ($this->StartResultCache(false, $id))
{
	if(!CModule::IncludeModule("iblock"))
		$this->AbortResultCache();

	$filter = array("IBLOCK_CODE"=>$iblock_code, "ID"=>$id, "ACTIVE_DATE"=>"Y","ACTIVE"=>"Y");
	
	if(is_array($arParams["FILTER"]))
		$filter = array_merge($filter,$arParams["FILTER"]);
	
 	 global $USER;

 	 /* $arr=array_intersect((array) $USER->GetUserGroupArray(),(array) $arParams["GROUPS_CAN_WATCH_INACTIVE"]);
	 if(!empty($arr)) */
 	 if($arParams['icanwatch']==true)
		unset($filter["ACTIVE"],$filter["ACTIVE_DATE"]);  	  
	 
	 
	$order  = array("sort"=>"asc","active_from"=>"desc","created_date"=>"desc");
	
	if(is_array($arParams["ORDER"]))
		$order = array_merge($arParams["ORDER"],$order);
	
	$select = array("ID","NAME","CODE","PREVIEW_PICTURE","PREVIEW_TEXT","DETAIL_PICTURE","DETAIL_TEXT","DETAIL_PAGE_URL");
	
	if(isset($arParams["SELECT"]))
	{
		if(is_array($arParams["SELECT"]))
			$select = array_merge($select,$arParams["SELECT"]);
		else
			$select[] = $arParams["SELECT"];
	}
	
	$res = CIBlockElement::GetList($order, $filter, false, false, $select);
	
	if ($ar_res = $res->GetNext())
	{
		$item = $ar_res; 

		$item["PREVIEW_PICTURE_SRC"] = CFile::GetPath($item["PREVIEW_PICTURE"]);//CFile::ResizeImageGet($item["PREVIEW_PICTURE"], array('width'=>150, 'height'=>150), BX_RESIZE_IMAGE_PROPORTIONAL, true);;
		$item["DETAIL_PICTURE_SRC"] = CFile::GetPath($item["DETAIL_PICTURE"]);
		
		$arResult["ITEM"] = $item;
	}

	if (count($arResult)<=0) {
		$this->AbortResultCache();
		if ($arParams['IBLOCK_CODE'] == "video")
			$APPLICATION->SetPageProperty('novideo', true);
	}
		
	
	$this->IncludeComponentTemplate();
}
if(isset($arResult["ITEM"]["ID"]))
	CIBlockElement::CounterInc($arResult["ITEM"]["ID"]);
?>