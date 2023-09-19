<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
    'NAME' => GetMessage('PROFILE_IB_NAME'),
    'DESCRIPTION' =>  GetMessage('PROFILE_IB_DESC'),
    'CACHE_PATH' => 'Y',
    'SORT' => 40,
    'COMPLEX' => 'N',
    'PATH' => array(
        'ID' => 'upfly',
        'NAME' =>  GetMessage('PROFILE_CUSTOM_COMPONENTS_NAME'),
    )
);
