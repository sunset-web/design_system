<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

// массив результата работы компонента
$arResult = array();
// Если есть ссылки
if (!empty($arParams['SOC_LINKS'])) {

	// if php version < 8
	if (!function_exists('str_ends_with')) {
		function str_ends_with($str, $end)
		{
			return (@substr_compare($str, $end, -strlen($end)) == 0);
		}
	}

	$arResult['LINKS'] = array();
	foreach ($arParams['SOC_LINKS'] as $key => $link) {
		//Собираем массив ссылок по одному ключу
		$icon = (!empty($arParams['SOC_ICONS']) ? $arParams['SOC_ICONS'][$key] : "");
		if (str_ends_with($icon, '.svg')) {
			$icon_html = file_get_contents($icon);
		} else if (stripos($icon, '.svg#')) {
			$icon_html = html_entity_decode('<svg><use xlink:href="' . $icon . '"></use></svg>');
		} else {
			$icon_html = html_entity_decode('<img src="' . $icon . '" alt="">');
		}
		$arResult['LINKS'][$key] = [
			'LINK' => $link,
			'ICON' => $icon_html
		];
	}
}


// подключаем шаблон компонента
$this->IncludeComponentTemplate();
