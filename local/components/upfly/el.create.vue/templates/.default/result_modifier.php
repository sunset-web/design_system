<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

Loader::includeModule('iblock');

// проверка доступа
$res = CIBlockElement::GetList(array(), array("IBLOCK_ID" => 1, "PROPERTY_PERSON_COMP" => \Bitrix\Main\Engine\CurrentUser::get()->getId()), false, false, array("ID"));
while ($obj = $res->GetNext()) $companiesIds[] = $obj['ID'];
if (!$companiesIds) {
  $companiesIds = array(0);
}
$arResult['FILTER_ACCESS'] = $companiesIds;
