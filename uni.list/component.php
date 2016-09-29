<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die (); ?>
<?
CPageOption::SetOptionString("main", "nav_page_in_session", "N");

$onpage = isset($arParams["ONPAGE"]) ? $arParams["ONPAGE"] : false;

$page = intval($arParams["PAGE"]);

$navigation = ($onpage) ? array('iNumPage' => $page, 'nPageSize' => $onpage) : false;

if ($navigation && $arParams['NAV'])
    $navigation = array_merge($navigation, $arParams['NAV']);

if (!$arParams["CACHE_TIME"])
    $arParams["CACHE_TIME"] = 3600;

if ($this->StartResultCache(false, serialize($navigation))) {
    if (!CModule::IncludeModule("iblock"))
        $this->AbortResultCache();

    $filter = array("IBLOCK_CODE" => $arParams["IBLOCK_CODE"], 'ACTIVE_DATE' => "Y", 'ACTIVE' => "Y");


    if (strlen($filter['IBLOCK_CODE']) == 0)
        unset($filter['IBLOCK_CODE']);

    if (isset($arParams["EXCLUDE_ID"])) {
        $filter["!ID"] = $arParams["EXCLUDE_ID"];
    }

    if (is_array($arParams["FILTER"])) {
        if ($arParams['FILTER'][0]['PREVIEW_TEXT'] === '')
            $arParams['FILTER'][0]['PREVIEW_TEXT'] = false;
        if ($arParams['FILTER'][0]['!PREVIEW_TEXT'] === '')
            $arParams['FILTER'][0]['!PREVIEW_TEXT'] = false;
        $filter = array_merge($filter, $arParams["FILTER"]);
    }

    $order = array("sort" => "asc", "active_from" => "desc", "created_date" => "desc");

    if (is_array($arParams["ORDER"]) && $arParams['ONLY_PARAMS_ORDER'] != "Y")
        $order = array_merge($order, $arParams["ORDER"]);

    if (is_array($arParams["ORDER"]) && $arParams['ONLY_PARAMS_ORDER'] == "Y")
        $order = $arParams['ORDER'];

    $select = array("ID", "NAME", "CODE", "PREVIEW_PICTURE", "PREVIEW_TEXT", "DETAIL_PICTURE", "DETAIL_TEXT", "DETAIL_PAGE_URL");

    if (isset($arParams["SELECT"])) {
        if (is_array($arParams["SELECT"]))
            $select = array_merge($select, $arParams["SELECT"]);
        else
            $select[] = $arParams["SELECT"];
    }

    if ($arParams["ELEMENT_TYPE"] == "section") {
        $res = CIBlockSection::GetList($order, $filter, true);
    } else {
        if (!empty($arParams['USE_CIBELEMENT'])) {

            if (empty($filter['IBLOCK_ID']) && !empty($filter['IBLOCK_CODE'])) {
                $filter['IBLOCK_ID'] = \InitFunctions::getIbIDByCode($filter['IBLOCK_CODE']);
            } else {
                throw new \Exception('unknown iblock');
            }

            if (!in_array('ID', $select))
                $select[] = 'ID';

            if (!in_array('IBLOCK_ID', $select))
                $select[] = 'IBLOCK_ID';

        }
        $res = CIBlockElement::GetList($order, $filter, false, $navigation, $select);
    }
    if ($onpage)
        $res->NavStart($onpage);
    $arResult["NavRecordCount"] = $res->NavRecordCount;//общее число элементов
    $arResult["NavPageSize"] = $res->NavPageSize;//количество элементов на странице
    $arResult['PAGES'] = $res->NavPageCount;

    if (empty($arParams['USE_CIBELEMENT'])) {
        while ($ar_res = $res->GetNext()) {
            $item = $ar_res;

            $item["PREVIEW_PICTURE_SRC"] = CFile::GetPath($item["PREVIEW_PICTURE"]);//CFile::ResizeImageGet($item["PREVIEW_PICTURE"], array('width'=>150, 'height'=>150), BX_RESIZE_IMAGE_PROPORTIONAL, true);;
            $item["DETAIL_PICTURE_SRC"] = CFile::GetPath($item["DETAIL_PICTURE"]);

            $arResult["ITEMS"][] = $item;
        }
    } else {
        while ($ar_res = $res->GetNextElement()) {
            $fields = $ar_res->GetFields();
            $props = $ar_res->GetProperties();

            $fields['PREVIEW_PICTURE_SRC'] = CFile::GetPath($fields['PREVIEW_PICTURE']);
            $fields['DETAIL_PICTURE_SRC'] = CFile::GetPath($fields['DETAIL_PICTURE']);

            $arResult['ITEMS'][] = array_merge($fields, $props);
        }
    }
    $arResult["COUNT"] = count($arResult["ITEMS"]);

    ob_start();
    $res->NavPrint('', false, false, '/bitrix/templates/' . SITE_TEMPLATE_ID . '/def_navigation.php');
    $navline = ob_get_contents();
    if (strlen($arParams["PAGEN"]) > 0)
        $navline = str_replace("/ajax/filmoteka.php/", htmlspecialchars($arParams["PAGEN"]), $navline);
    ob_end_clean();
    $arResult["NAV_STRING"] = $navline;

    if (count($arResult) <= 0)
        $this->AbortResultCache();

    $this->IncludeComponentTemplate();
}