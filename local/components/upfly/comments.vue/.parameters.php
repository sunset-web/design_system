<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentParameters = array(
  "PARAMETERS" => array(
    "ID" => array(
      "PARENT" => "BASE",
      "NAME" => 'ID сущности',
      "TYPE" => "STRING",
      "DEFAULT" => '={$_REQUEST["ID"]}',
    ),
    "OFFSET" => array(
      "PARENT" => "BASE",
      "NAME" => 'Начало выборки',
      "TYPE" => "STRING",
      "DEFAULT" => '={$_REQUEST["OFFSET"]}',
    ),
  ),
);
