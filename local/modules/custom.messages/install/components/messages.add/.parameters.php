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
	)
);
