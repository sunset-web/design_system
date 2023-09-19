<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

// массив результата работы компонента
$arResult = array();
if (CModule::IncludeModule('iblock')) {
    // получаем из базы данных элемент инфоблока по идентификатору;
    // идентификатор получаем из входных параметров компонента
    $props_exc = ["ID" =>"ID", "PASSWORD" =>"PASSWORD", "CHECKWORD" =>"CHECKWORD", "PASSWORD_EXPIRED" =>"PASSWORD_EXPIRED", "BX_USER_ID" =>"BX_USER_ID", "XML_ID" =>"XML_ID", "EXTERNAL_AUTH_ID" =>"EXTERNAL_AUTH_ID"];
    $propsParams = array_diff($arParams["USER_PROPS"], array(''));

    $rsUser = CUser::GetList($by, $order,
        array("ID" => (int)$arParams['USER_ID']),
        array()
    );
    if($arUser = $rsUser->Fetch())
    {
        if(!empty($propsParams)) {
            foreach($propsParams as $param) {
                $arResult["USER"][$param] = $arUser[$param];
            }
        } else {
            foreach($arUser as $key => $prop) {
                if(!array_key_exists($key, $props_exc)) {
                    $arResult["USER"][$key] = $prop;
                }
            }
        }
        if(!empty($arParams["USER_COVER_IMG"])) $arResult["USER"]["USER_AVATAR_COVER"] = $arParams["USER_COVER_IMG"];
        if(!empty($arParams["USER_PROFILE_LINK"])) $arResult["USER"]["USER_PROFILE_LINK"] = $arParams["USER_PROFILE_LINK"];
    }
}
// подключаем шаблон компонента
$this->IncludeComponentTemplate();