<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var string $componentPath
 * @var string $componentName
 * @var array $arCurrentValues
 * */

use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;

if (!Loader::includeModule("highloadblock")) {
	throw new \Exception('Не загружены модули необходимые для работы компонента');
}
// Получаем список hlblocks
$arHlData = HighloadBlockTable::getList(array(
	'select' => array("ID", "NAME"),
	'order' => array('ID' => 'ASC'),
));
while ($arHlbk = $arHlData->Fetch()) {
	$arrHlblocks[$arHlbk['ID']] = $arHlbk['NAME'] . '[' . $arHlbk['ID'] . ']';
}

// Получаем список свойств
$obUserField  = new CUserTypeEntity;
$rsData = \CUserTypeEntity::GetList(array("SORT" => "ASC"), array(
	"ENTITY_ID" => 'HLBLOCK_' . $arCurrentValues["HLBLOCKS_ID"],
));
while ($arRes = $rsData->Fetch()) {
	$arrHlblocksProp[$arRes['FIELD_NAME']] = $arRes['FIELD_NAME'] . '[' . $arRes['ID'] . ']';
}

$arComponentParameters = array(
	"PARAMETERS" => array(
		"HLBLOCKS_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MESSAGES_LIST_PAGE_HLBLOCKS_ID"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"SIZE" => "6",
			"VALUES" => $arrHlblocks,
			"REFRESH" => "Y",
		),
		"PAGE_ELEMENT_COUNT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MESSAGES_LIST_PAGE_ELEMENT_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "12",
		),
		"FIELDS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MESSAGES_LIST_FIELDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"SIZE" => "10",
			"VALUES" => $arrHlblocksProp,
		),
		"SHOW_PAGER" => array(
			"PARENT" => "BASE",
			"NAME" =>  GetMessage("MESSAGES_LIST_SHOW_PAGER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y"
		),

		"CACHE_TIME"  =>  array("DEFAULT" => 3600),
	)
);
