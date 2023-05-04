<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
	"NAME" => Loc::getMessage("UPFLY_SOC_LIST"),
	"DESCRIPTION" => Loc::getMessage("UPFLY_SOC_LIST_COMPONENT_DESCRIPTION"),
	"COMPLEX" => "N",
    "CACHE_PATH" => "Y",
	"PATH" => [
         'ID' => 'upfly',
        "NAME" => Loc::getMessage("UPFLY_SOC_LIST_COMPONENT_PATH_NAME"),
	],
];
?>