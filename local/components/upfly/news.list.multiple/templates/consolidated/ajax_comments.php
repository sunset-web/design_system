<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

$APPLICATION->IncludeComponent(
  'itin:comments',
  '',
  array(
    "ID" => $request->get('ID'),
    "OFFSET" => $request->get('OFFSET'),
  ),
  false
);
