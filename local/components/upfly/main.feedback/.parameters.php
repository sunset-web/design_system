<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$site = ($_REQUEST["site"] != '' ?
	$_REQUEST["site"] : ($_REQUEST["src_site"] != '' ?
		$_REQUEST["src_site"] : false
	)
);

$arFilter = ["TYPE_ID" => "FEEDBACK_FORM", "ACTIVE" => "Y"];
if ($site !== false)
	$arFilter["LID"] = $site;

// список событий
$arEvent = [];
$dbType = CEventMessage::GetList("id", "desc", $arFilter);
while ($arType = $dbType->GetNext()) {

	$arEvent[$arType["ID"]] = "[" . $arType["ID"] . "] " . $arType["SUBJECT"];
}


// список типов инфоблоков
$resultQueryType = \Bitrix\Iblock\TypeTable::getList([
	'cache' => [
		'ttl' => 3600000,
		'cache_joins' => true,
	],
	'filter' => ['=LANG_MESSAGE.LANGUAGE_ID' => 'ru'],
	'select'  =>  [
		'ID',
		'NAME' => 'LANG_MESSAGE.NAME',

	],
])->FetchAll();

$arTypesEx = [];
foreach ($resultQueryType as $item) {

	$arTypesEx[$item['ID']] = "[" . $item["ID"] . "] " . $item['NAME'];
}

// список инфоблоков
$arFilterIblock = ['=IBLOCK_TYPE_ID' => $arCurrentValues["IBLOCK_TYPE"]];
if ($site !== false)
	$arFilterIblock["=LID"] = $site;

$resultQueryIblock = \Bitrix\Iblock\IblockTable::getList([
	'filter' => $arFilterIblock,
	'cache' => [
		'ttl' => 3600,
		'cache_joins' => true,
	],
	'select'  =>  [
		'ID', 'NAME'

	],
])->FetchAll();

$arIBlocks = [];
foreach ($resultQueryIblock as $item) {

	$arIBlocks[$item['ID']] = "[" . $item["ID"] . "] " . $item['NAME'];
}

$arComponentParameters = array(
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => \Bitrix\Main\Localization\Loc::getMessage("T_IBLOCK_DESC_LIST_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "forms",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => \Bitrix\Main\Localization\Loc::getMessage("T_IBLOCK_DESC_LIST_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '',
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
		"TITLE_FORM" => array(
			"NAME" => \Bitrix\Main\Localization\Loc::getMessage("MFP_TITLE_FORM"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "BASE",
		),
		"POLICY_TEXT" => array(
			"NAME" => \Bitrix\Main\Localization\Loc::getMessage("MFP_POLICY_TEXT"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "BASE",
		),
		"POLICY_LINK" => array(
			"NAME" => \Bitrix\Main\Localization\Loc::getMessage("MFP_POLICY_LINK"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "BASE",
		),
		"OK_TEXT" => array(
			"NAME" => \Bitrix\Main\Localization\Loc::getMessage("MFP_OK_MESSAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => \Bitrix\Main\Localization\Loc::getMessage("MFP_OK_TEXT"),
			"PARENT" => "BASE",
		),
		"EMAIL_TO" => array(
			"NAME" => \Bitrix\Main\Localization\Loc::getMessage("MFP_EMAIL_TO"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "BASE",
		),
		"EVENT_MESSAGE_ID" => array(
			"NAME" => \Bitrix\Main\Localization\Loc::getMessage("MFP_EMAIL_TEMPLATES"),
			"TYPE" => "LIST",
			"VALUES" => $arEvent,
			"DEFAULT" => "",
			"MULTIPLE" => "Y",
			"COLS" => 25,
			"PARENT" => "BASE",
		),
		"USE_CAPTCHA" => array(
			"NAME" => \Bitrix\Main\Localization\Loc::getMessage("MFP_CAPTCHA"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"PARENT" => "BASE",
		),

	)
);
