<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if (!CModule::IncludeModule("iblock"))
   return;
$arIBlocks = array();
$db_iblock = CIBlock::GetList(array("SORT" => "ASC"), array("SITE_ID" => $_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"] != "-" ? $arCurrentValues["IBLOCK_TYPE"] : "")));
while ($arRes = $db_iblock->Fetch())
   $arIBlocks[$arRes["ID"]] = "[" . $arRes["ID"] . "] " . $arRes["NAME"];

$arOptions = array(
   'NAME' => '[NAME] Название',
   'PREVIEW_TEXT' => '[PREVIEW_TEXT] Текст анонса',
   'PREVIEW_PICTURE' => '[PREVIEW_PICTURE] Картинка анонса',
   'DETAIL_TEXT' => '[DETAIL_TEXT] Текст детальной',
   'DETAIL_PICTURE' => '[DETAIL_PICTURE] Картинка детальной',
);

$arProperty_LNS = array();
$rsProp = CIBlockProperty::GetList(array("sort" => "asc", "name" => "asc"), array("ACTIVE" => "Y", "IBLOCK_ID" => (isset($arCurrentValues["IBLOCK_ID"]) ? $arCurrentValues["IBLOCK_ID"] : $arCurrentValues["ID"])));
while ($arr = $rsProp->Fetch()) {
   $arProperty_LNS[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
}

$arProperty_OB = [];
foreach ($arCurrentValues["FIELDS"] as $code) {

   if ($code) {
      $arr = CIBlockProperty::GetList(
         array("sort" => "asc", "name" => "asc"),
         array("ACTIVE" => "Y", "IBLOCK_ID" => (isset($arCurrentValues["IBLOCK_ID"]) ? $arCurrentValues["IBLOCK_ID"] : $arCurrentValues["ID"]), "CODE" => $code)
      )->fetch();

      if ($arr["NAME"]) {
         $arProperty_OB[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
      }
   }
}

$arOptions_OB = [];
foreach ($arCurrentValues['OPTIONS'] as $code) {
   if ($code && $arOptions[$code]) {
      $arOptions_OB[$code] = $arOptions[$code];
   }
}

$allProps = array_merge($arOptions, $arProperty_LNS);

$ar_OB = array_diff(array_merge($arOptions_OB, $arProperty_OB), array(''));

$arComponentParameters = array(
   "PARAMETERS" => array(
      "IBLOCK_ID" => array(
         "PARENT" => "BASE",
         "NAME" => GetMessage('IBLOCK_ID_NAME'),
         "TYPE" => "LIST",
         "VALUES" => $arIBlocks,
         "DEFAULT" => '={$_REQUEST["ID"]}',
         "ADDITIONAL_VALUES" => "Y",
         "REFRESH" => "Y",
      ),
      "OPTIONS" => array(
         "PARENT" => "BASE",
         "NAME" => GetMessage('OPTIONS_NAME'),
         "TYPE" => "LIST",
         "MULTIPLE" => "Y",
         "VALUES" => $arOptions,
         "ADDITIONAL_VALUES" => "Y",
         "REFRESH" => "Y",
      ),
      "FIELDS" => array(
         "PARENT" => "BASE",
         "NAME" => GetMessage('FIELDS_NAME'),
         "TYPE" => "LIST",
         "MULTIPLE" => "Y",
         "VALUES" => $arProperty_LNS,
         "ADDITIONAL_VALUES" => "Y",
         "REFRESH" => "Y",
      ),
      "LINK_ADD" => array(
         "PARENT" => "BASE",
         "NAME" => GetMessage('LINK_ADD_NAME'),
         "TYPE" => "LIST",
         "MULTIPLE" => "Y",
         "VALUES" => $arProperty_LNS,
         "ADDITIONAL_VALUES" => "Y",
         "REFRESH" => "Y",
      ),
      "REQUIRED_FIELDS" => array(
         "PARENT" => "BASE",
         "NAME" => GetMessage('REQUIRED_FIELDS_NAME'),
         "TYPE" => "LIST",
         "MULTIPLE" => "Y",
         "VALUES" => $ar_OB,
         "ADDITIONAL_VALUES" => "Y",
      ),
      "NO_EDITABLE_FIELDS" => array(
         "PARENT" => "BASE",
         "NAME" => GetMessage('NO_EDITABLE_FIELDS_NAME'),
         "TYPE" => "LIST",
         "MULTIPLE" => "Y",
         "VALUES" => $allProps,
         "ADDITIONAL_VALUES" => "Y",
      ),
      "ELEMENT_ID" => array(
         "PARENT" => "BASE",
         "NAME" => GetMessage('ELEMENT_ID'),
         "TYPE" => "STRING",
      ),
      "DELETE_BTN" => array(
         "PARENT" => "BASE",
         "NAME" => GetMessage('DELETE_BTN'),
         "TYPE" => "CHECKBOX"
      )

   ),
);
