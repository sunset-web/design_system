<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $USER;
$arProps = array();
$props_exc = ["ID" =>"ID", "PASSWORD" =>"PASSWORD", "CHECKWORD" =>"CHECKWORD", "PASSWORD_EXPIRED" =>"PASSWORD_EXPIRED", "BX_USER_ID" =>"BX_USER_ID", "XML_ID" =>"XML_ID", "EXTERNAL_AUTH_ID" =>"EXTERNAL_AUTH_ID"];
$rsUser = CUser::GetByID($USER->GetID());
$arUser = $rsUser->Fetch();
if(!empty($arUser)) {
    foreach($arUser as $key => $prop) {
        if(!array_key_exists($key, $props_exc)) {
            $arProps[$key] = $key;
        }
    }
}


$arComponentParameters = array(
    'GROUPS' => array(),
    'PARAMETERS' => array(
        'USER_ID' => array(
            'PARENT' => 'BASE', 
            'NAME' => GetMessage('PROFILE_PARAMS_USER_ID_LABEL'), 
            'TYPE' => 'STRING', 
            'MULTIPLE' => 'N',  
            'DEFAULT' => '', 
            'REFRESH' => 'Y',   
        ),
        'USER_COVER_IMG' => array(
            'PARENT' => 'BASE', 
            'NAME' => GetMessage('PROFILE_PARAMS_USER_COVER_IMG_LABEL'), 
            'TYPE' => 'STRING', 
            'MULTIPLE' => 'N',  
            'DEFAULT' => '', 
            'REFRESH' => 'Y',   
        ),
        'USER_PROFILE_LINK' => array(
            'PARENT' => 'BASE', 
            'NAME' => GetMessage('PROFILE_PARAMS_USER_PROFILE_LINK_LABEL'), 
            'TYPE' => 'STRING', 
            'MULTIPLE' => 'N', 
            'DEFAULT' => '', 
            'REFRESH' => 'Y',  
        ),
        'USER_PROPS' => array(
            'PARENT' => 'BASE', 
            'NAME' => GetMessage('PROFILE_PARAMS_USER_PROPS_LABEL'), 
            'TYPE' => 'LIST',
            'MULTIPLE'=>'Y', 
            "ADDITIONAL_VALUES" => "Y", 
            "VALUES" => $arProps,
            'DEFAULT' => array(), 
            'REFRESH' => 'Y',   
        ),
        'CACHE_TIME' => ['DEFAULT' => 3600],
    ),
);