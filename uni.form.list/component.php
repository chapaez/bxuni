<?if (! defined ( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true) die ();?>

<?

if (intval($arParams['STATUS_ID']) <= 0)
    exit('Bad active status ID');

if (intval($arParams['FORM_ID']) <= 0)
    exit('form ID is required');

$limit = ($arParams['LIMIT']) ? $arParams['LIMIT'] : 5000;
$onPage = ($arParams['ONPAGE']) ? $arParams['ONPAGE'] : 9;

/*if ($this->StartResultCache(false)) {*/

    if (CModule::IncludeModule("form")) {
        CPageOption::SetOptionString("main", "nav_page_in_session", "N");

        $arFilter = array(
            'STATUS_ID' => $arParams['STATUS_ID']
        );

        $rsResults = CFormResult::GetList($arParams['FORM_ID'],
            ($by="s_date_create"),
            ($order="desc"),
            $arFilter,
            $is,
            "Y",
            $limit);


        // gets all results
        $ids = array();
        while ($result = $rsResults->Fetch()) {
            $results[] = $result;
            $ids[] = $result['ID'];
        }
        if (count($ids)>0) {
            $arResult=Array();
            $fields=Array();$ans=Array();$ans2=Array();
            $arAnswers = CForm::GetResultAnswerArray(
                $arParams['FORM_ID'],
                $fields,
                $ans,
                $ans2,
                array(
                    'RESULT_ID' => implode(" | ", $ids)
                )
            );
           // mdump($ans);
            //die();
            $arResult['FIELDS']=$fields;
            $arResult['ANSWERS']=$ans;
            $arResult['ANSWERS_2']=$ans2;

            krsort($arResult['ANSWERS_2']);
            foreach ($arResult['FIELDS'] as $field) {
                $comments[$field['SID']] = $field['COMMENTS'];
                $sort[$field['SID']] = $field['C_SORT'];
            }


            foreach ($arResult['ANSWERS_2'] as $res_id => $res) {
                foreach ($res as $sid => $arAnswer) {
                    $arAnswer[0]['COMMENTS'] = $comments[$arAnswer[0]['SID']];
                    $arAnswer[0]['C_SORT'] = $sort[$arAnswer[0]['SID']];
                    $arResult['CLEAR_ANSWERS'][$res_id][] = $arAnswer[0];
                }
            }

            // have to create cdbresult from array for pagenav
            $rs = new CDBResult;
            $rs->InitFromArray($arResult['CLEAR_ANSWERS']);

            $page = isset($arParams['PAGE']) ? $arParams['PAGE'] : 1;

            // TODO: deprecated third arg
            $rs->NavStart($onPage, true, $page);

            $arResult['CLEAR_ANSWERS'] = array();

            while ($nav_result = $rs->Fetch()) {
                $arResult['CLEAR_ANSWERS'][$nav_result[0]['RESULT_ID']] = $nav_result;
            }
            $arResult['PAGES'] = $rs->NavPageCount;
            $arResult['CUR_PAGE'] = $page;

            /*if (count($arResult) <= 0)
                $this->AbortResultCache();*/

            $arResult['URL'] = ($arParams['URL']) ? $arParams['URL'] : '';
        } else {
            $arResult['BLANK_RESULT_TEXT'] = $arParams['BLANK_RESULT_TEXT'];
            $arResult['CLEAR_ANSWERS'] = array();
        }

    }

    $template = $arParams['TEMPLATE'] ?: '';
    $this->IncludeComponentTemplate($template);
/*}*/
