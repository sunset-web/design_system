<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/**
 * @var string $componentPath
 * @var string $componentName
 * @var array $arCurrentValues
 * */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if( !Loader::includeModule("iblock") ) {
    throw new \Exception('Не загружены модули необходимые для работы компонента');
}

$arComponentParameters = [
    // группы в левой части окна
    "GROUPS" => [
        "SETTINGS" => [
            "NAME" => Loc::getMessage('UPFLY_SOC_LIST_TITLE_PARAMS'),
            "SORT" => 550,
        ],
    ],
    // поля для ввода параметров в правой части
    "PARAMETERS" => [
        // Произвольный параметр типа СТРОКА
        "SOC_LINKS" => [
            "PARENT" => "SETTINGS",
            "NAME" => Loc::getMessage('UPFLY_SOC_LIST_LINK_PARAM'),
            "TYPE" => "STRING",
            "MULTIPLE" => "Y",
            "DEFAULT" => "",
            "COLS" => 25
        ],
        // Произвольный параметр типа СТРОКА
        "SOC_ICONS" => [
            "PARENT" => "SETTINGS",
            "NAME" => Loc::getMessage('UPFLY_SOC_LIST_LINK_ICON'),
            "TYPE" => "STRING",
            "MULTIPLE" => "Y",
            "DEFAULT" => "",
            "COLS" => 25
        ],
        // Настройки кэширования
        'CACHE_TIME' => ['DEFAULT' => 3600],
    ]
];
