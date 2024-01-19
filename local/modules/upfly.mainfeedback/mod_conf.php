<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arCstmProps
 * пользовательские поля
 * элемент массива ["объект для добавления поля", "UF_код", "тип", "название"]
 */
$arCstmProps = [
    // кастомные поля пользователя
    ['USER', 'UF_FIO_DIR', 'string', 'ФИО Директора'],
    ['USER', 'UF_UR_ADR', 'string', 'Юридический адрес'],
];

/**
 * @var array $arTables
 * ORM таблицы
 * Элемент массива - "имя класса таблицы", сама таблица должна быть описана в /lib/tables/
 */
$arTables = [
    'TicketCategory',
    'TicketStatus',
    'TicketRating',
    'TicketCategory',
    'TicketMsg',
    'Ticket',
];

/**
 * @var array $arIndexes
 * индексы ORM таблиц
 * элемент массива - ["имя класса таблицы", "имя поля таблицы"]
 */
$arIndexes = [
    ['Ticket', 'client_id'],
    ['Ticket', 'manager_id'],
    ['Ticket', 'ticket_status_id'],
    ['Ticket', 'ticket_rating_id'],
    ['Ticket', 'ticket_category_id'],
    ['TicketMsg', 'ticket_id'],
    ['TicketMsg', 'sender_id'],
];

/**
 * @var array $arIblockTypes
 * типы инфоблоков
 */
$arIblockTypes = [
    'FORMS' => [
        'SECTIONS' => 'N',
        'SORT' => '100',
        'LANG' => [
            'ru' => [
                'NAME'=>'Формы',
                'ELEMENT_NAME'=>'Формы'
            ]
        ]
    ],
];

/**
 * @var array $arIblocks
 * инфоблоки
 */
$arIblocks = [
    'FEEDBACK' => [
        'TYPE' => 'FORMS',
        'NAME' => 'Форма обратной связи',

        'PROPS' => [
            ['NAME' => 'Имя', 'CODE' => 'NAME'],
            ['NAME' => 'Телефон', 'CODE' => 'PHONE'],
            ['NAME' => 'Почта', 'CODE' => 'MAIL'],
            ['NAME' => 'Комментарий', 'CODE' => 'COMMENT'],
            ['NAME' => 'Заголовок', 'CODE' => 'TITLE'],
        ]
    ],
];

/**
 * @var array $arEmailTmpls
 * почтовые шаблоны
 */
$arEmailTmpls = [
    [
        "ACTIVE" => "Y",
        "EVENT_NAME" => "FEEDBACK_FORM",
        "EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
        "EMAIL_TO" => "#DEFAULT_EMAIL_FROM#",
        "BCC" => "",
        "SUBJECT" => "Заявка с сайта #SITE_NAME#",
        "BODY_TYPE" => "text",
        "MESSAGE" => "
Скрытый заголовок: #TITLE#
Имя: #NAME#
Телефон: #PHONE#
Почта: #MAIL#
Комментарий: #COMMENT#
        ",
    ],
];

/**
 * @var array $arSalePersonTypes
 * типы плательщиков
 */
$arSalePersonTypes = [
    ['NAME' => 'Тестовый тип плательщика',],
];

/**
 * @var array $arSaleOrderPropsGroups
 * группы свойств заказа
 */
$arSaleOrderPropsGroups = [
    [
        "PERSON_TYPE_NAME" => 'Физическое лицо',
        'NAME' => 'Служебные',
    ],
];

/**
 * @var array $arSaleOrderProps
 * свойства заказа
 * Допустимые ключи:
 */
$arSaleOrderProps = [
    [
        "PERSON_TYPE_NAME" => 'Физическое лицо',
        "PROPS_GROUP_NAME" => 'Служебные',
        "NAME" => "Служебное Требуется передать в 1С",
        "TYPE" => "TEXT",
        "CODE" => "EXPORT_DO",
    ],

    [
        "PERSON_TYPE_NAME" => 'Физическое лицо',
        "PROPS_GROUP_NAME" => 'Служебные',
        "NAME" => "Служебное Получен из 1С",
        "TYPE" => "TEXT",
        "CODE" => "IS_IMPORTED",
    ],

    [
        "PERSON_TYPE_NAME" => 'Физическое лицо',
        "PROPS_GROUP_NAME" => 'Служебные',
        "NAME" => "Служебное дата запроса",
        "TYPE" => "TEXT",
        "CODE" => "EDIT_REQUEST_DT",
    ],

    [
        "PERSON_TYPE_NAME" => 'Физическое лицо',
        "PROPS_GROUP_NAME" => 'Служебные',
        "NAME" => "Служебное дата подтверждения",
        "TYPE" => "TEXT",
        "CODE" => "EDIT_RESPONS_DT",
    ],
];

$baseDir = basename(__DIR__);
$moduleName = strtoupper($baseDir);
$baseNS = 'Local';
$parts = explode('.', $baseDir);
$moduleNS = $baseNS . '\\' . ucfirst($parts[1]);

$arConfig = [
    'id' => strtolower($moduleName),
    'name' => $moduleName,
    'ns' => $moduleNS,
    'nsTables' => $moduleNS . '\Tables',
    'prefix' => 'mainfeedback',
    // 'arCstmProps' => $arCstmProps,
    'arIblockTypes' => $arIblockTypes,
    'arIblocks' => $arIblocks,
    'arEmailTmpls' => $arEmailTmpls,
];

return $arConfig;
