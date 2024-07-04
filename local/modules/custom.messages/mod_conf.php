<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arHLblocks
 * хайлоадблоки
 */
$arHLblocks = [
	'Messages' => [
		'NAME' => ['ru' => 'Сообщения'],
		'NAME_ENTITY' => 'Messages',
		'TABLE_NAME' => 'messages',

		'PROPS' => [
			'UF_DATE' => [
				'USER_TYPE_ID' => 'datetime',
				'MANDATORY' => '',
				'EDIT_FORM_LABEL' => ['ru' => 'Дата сообщения'],
				'LIST_COLUMN_LABEL' => ['ru' => 'Дата сообщения'],
				'SETTINGS' => [
					'DEFAULT_VALUE' => ['TYPE' => 'NOW']
				],
			],
			'UF_TEXT' => [
				'USER_TYPE_ID' => 'string',
				'MANDATORY' => '',
				'EDIT_FORM_LABEL' => ['ru' => 'Текс сообщения'],
				'LIST_COLUMN_LABEL' => ['ru' => 'Текс сообщения'],
				'SETTINGS' => [
					'SIZE' => 30,
					'ROWS' => 5,
				]
			],
			'UF_USER' => [
				'USER_TYPE_ID' => 'userid',
				'MANDATORY' => '',
				'EDIT_FORM_LABEL' => ['ru' => 'Пользователь'],
				'LIST_COLUMN_LABEL' => ['ru' => 'Пользователь'],
			],
		]
	],
];

/**
 * @var array $arEmailTypes
 * почтовые события
 */
$arEmailTypes = [
	[
		"EVENT_NAME"  => "NEW_MESSAGE",
		"NAME"        => "Новое сообщение",
		"LID"         => "ru",
		"SORT"        => 100,
		"DESCRIPTION" => "
#USER# - Имя пользователя
#TEXT# - Текст сообщения
        "
	]
];

/**
 * @var array $arEmailTmpls
 * почтовые шаблоны
 */
$arEmailTmpls = [
	[
		"ACTIVE" => "Y",
		"EVENT_NAME" => "NEW_MESSAGE",
		"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
		"EMAIL_TO" => "#DEFAULT_EMAIL_FROM#",
		"BCC" => "",
		"SUBJECT" => "На сайте #SITE_NAME# новое сообщение",
		"BODY_TYPE" => "text",
		"MESSAGE" => "На сайте #SITE_NAME# новое сообщение:
		#USER#: #TEXT#",
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
	'prefix' => 'custommessages',
	'arHLblocks' => $arHLblocks,
	'arEmailTypes' => $arEmailTypes,
	'arEmailTmpls' => $arEmailTmpls,
];

return $arConfig;
