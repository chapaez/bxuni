<?if (! defined ( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true) die ();?>

<?

if (intval($arParams['STATUS_ID']) <= 0)
    exit('Bad active status ID');

if (intval($arParams['RESULT_ID']) <= 0)
    exit('Result ID is required');

if (intval($arParams['FORM_ID']) <= 0)
    exit('form ID is required');

if ($this->StartResultCache(false, $result_id.serialize($_GET))) {

    if (CModule::IncludeModule("form")) {


        $arFilter = array(
            'ID'        => $arParams['RESULT_ID'],
            'STATUS_ID' => $arParams['STATUS_ID']
        );
        
        $rsResults = CFormResult::GetList($arParams['FORM_ID'],
            ($by="s_timestamp"),
            ($order="desc"),
            $arFilter,
            $is_filtered);
        
        if ($arResult = $rsResults->Fetch()) {
            $arAnswer = CForm::GetResultAnswerArray( 
                $arParams['FORM_ID'],
                $arResult['FIELDS'],
                $arResult['ANSWERS'],
                $arResult['ANSWERS_2'],
                array(
                    'RESULT_ID' => $arParams['RESULT_ID']
                ));

        }
        
        
        foreach ($arResult['FIELDS'] as $field) {
            $comments[$field['SID']] = $field['COMMENTS'];
            $sort[$field['SID']]      = $field['C_SORT'];
        }
        
        
        foreach ($arResult['ANSWERS_2'][$arParams['RESULT_ID']] as $id => $arAnswer) {
                $arAnswer[0]['COMMENTS'] = $comments[$arAnswer[0]['SID']];
                $arAnswer[0]['C_SORT']   = $sort[$arAnswer[0]['SID']];
                $arResult['CLEAR_ANSWERS'][] = $arAnswer[0];
        }
        
        if (count($arResult) <= 0)
            $this->AbortResultCache();

       
    }
    
    $this->IncludeComponentTemplate();
}
