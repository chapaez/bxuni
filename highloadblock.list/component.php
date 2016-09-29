<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!$arParams["CACHE_TIME"])
	$arParams["CACHE_TIME"] = 3600;
	

	$requiredModules = array('highloadblock');
	
	foreach ($requiredModules as $requiredModule)
	{
		if (!CModule::IncludeModule($requiredModule))
		{
			ShowError(GetMessage("F_NO_MODULE"));
			return 0;
		}
	}
	
	use Bitrix\Highloadblock as HL;
	use Bitrix\Main\Entity;
	use Bitrix\Main\Entity\ExpressionField;
	global $CACHE_MANAGER;
	
if($this->StartResultCache()){
	// hlblock info
	$hlblock_id = $arParams['BLOCK_ID'];
	
	if (empty($hlblock_id))
	{
		ShowError(GetMessage('HLBLOCK_LIST_NO_ID'));
		return 0;
	}
	
	$hlblock = HL\HighloadBlockTable::getById($hlblock_id)->fetch();
	
	if (empty($hlblock))
	{
		ShowError('404');
		return 0;
	}
	
	$entity = HL\HighloadBlockTable::compileEntity($hlblock);
	
	// uf info
	$fields = $GLOBALS['USER_FIELD_MANAGER']->GetUserFields('HLBLOCK_'.$hlblock['ID'], 0, LANGUAGE_ID);
	
	// pagination
	$limit = array(
		'nPageSize' => $arParams['ROWS_PER_PAGE'],
		'iNumPage' => is_set($_GET['PAGEN_1']) ? $_GET['PAGEN_1'] : 1,
		'bShowAll' => true
	);
	
	$CACHE_MANAGER->StartTagCache('/'.SITE_ID.$this->GetRelativePath().'/');
	$CACHE_MANAGER->RegisterTag("hlblock_".$arParams["BLOCK_ID"]);
	if($arParams["ADDITIONAL_CACHE_TAG"])
		$CACHE_MANAGER->RegisterTag("hlblock_add_".$arParams["ADDITIONAL_CACHE_TAG"]);
	$CACHE_MANAGER->EndTagCache();
	// sort
	$sort_id = 'ID';
	$sort_type = 'ASC';
	
	if (!empty($arParams['sort_id']) && (isset($fields[$arParams['sort_id']])))
	{
		$sort_id = $arParams['sort_id'];
	}
	
	if (!empty($arParams['sort_type']) && in_array($arParams['sort_type'], array('ASC', 'DESC'), true))
	{
		$sort_type = $arParams['sort_type'];
	}
	
	// limit
	$limit = array(
		'nPageSize' => $arParams['ROWS_PER_PAGE'],
		'iNumPage' => is_set($_GET['PAGEN_1']) ? $_GET['PAGEN_1'] : 1,
		'bShowAll' => true
	);
	
	
	
	// execute query
	
	$main_query = new Entity\Query($entity);
	$main_query->setSelect(array('*'));
	$main_query->setOrder(array($sort_id => $sort_type));
	//$main_query->setSelect($select)
	//	->setFilter($filter)
	//	->setGroup($group)
	//	->setOrder($order)
	//	->setOptions($options);
	
	
	if (isset($limit['nPageTop']))
	{
		$main_query->setLimit($limit['nPageTop']);
	}
	else
	{
		$main_query->setLimit($limit['nPageSize']);
		$main_query->setOffset(($limit['iNumPage']-1) * $limit['nPageSize']);
	}
	
	//$main_query->setLimit($limit['nPageSize']);
	//$main_query->setOffset(($limit['iNumPage']-1) * $limit['nPageSize']);
	$main_query->setFilter($arParams['FILTER']);
	
	$main_query->countTotal(true);
	//mdump($main_query->countTotal());
	
	//InitFunctions::TO($main_query);
	$result = $main_query->exec();
	//общее количество - отдельным запросом.
	$main_query->setLimit(NULL);
	$main_query->setOffset(NULL);
	$total_count_obj = $main_query->exec();
	$arResult['total_count'] = $total_count_obj->getSelectedRowsCount();
	
	//mdump($main_query->countTotal());
	$result = new CDBResult($result);

	
	//InitFunctions::TO($result);
	
	//mdump(get_object_vars($result));
	// build results
	$rows = array();
	
	$tableColumns = array();
	
	while ($row = $result->Fetch())
	{//mdump($row);
		foreach ($row as $k => $v)
		{
			if ($k == 'ID')
			{
				$tableColumns['ID'] = true;
				continue;
			}
	
			$arUserField = $fields[$k];
	
			//mdump($arUserField);
			if ($arUserField["SHOW_IN_LIST"]!="Y")
			{
				continue;
			}
	
			$html = call_user_func_array(
				array($arUserField["USER_TYPE"]["CLASS_NAME"], "getadminlistviewhtml"),
				array(
					$arUserField,
					array(
						"NAME" => "FIELDS[".$row['ID']."][".$arUserField["FIELD_NAME"]."]",
						"VALUE" => htmlspecialcharsbx($v)
					)
				)
			);
	
			if($html == '')
			{
				$html = '&nbsp;';
			}
	
			$tableColumns[$k] = true;
			
			$row[$k] = $html;
			$row['~'.$k] = $v;
		}
	

		$rows[] = $row;
	}
	
	
	$arResult["NAV_STRING"] = $result->GetPageNavString('', (is_set($arParams['NAV_TEMPLATE'])) ? $arParams['NAV_TEMPLATE'] : 'arrows');
	$arResult["NAV_PARAMS"] = $result->GetNavParams();
	$arResult["NAV_NUM"] = $result->NavNum;
	
	$arResult['rows'] = $rows;
	$arResult['fields'] = $fields;
	$arResult['tableColumns'] = $tableColumns;
	
	$arResult['sort_id'] = $sort_id;
	$arResult['sort_type'] = $sort_type;
	
	
	$this->IncludeComponentTemplate();
}