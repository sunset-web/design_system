<?php

namespace Custom\Messages;

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\UserField;


/*
 * Пояснения:
 * (*)  - Мы принимаем массив array('VALUE' => , 'DESCRIPTION' => ) и должны его же вернуть. Если поле с описанием - оно будет содержаться в соответствующем ключе.
 */

class UsersProperty
{
	public static function GetUserTypeDescription()
	{
		return array(
			"USER_TYPE_ID" => 'userid',
			"CLASS_NAME" => __CLASS__,
			"DESCRIPTION" => 'Привязка к пользователю',
			"BASE_TYPE" => \CUserTypeManager::BASE_TYPE_INT,
		);
	}
	//Получаем список значений
	public static function GetList($arUserField)
	{
		$rsEnum = [];
		$dbResultList = \CUser::GetList(($by = 'id'), ($order = 'asc'));
		while ($arResult = $dbResultList->Fetch()) {
			$rsEnum[] = [
				'ID' => $arResult['ID'],
				//Формат отображения значений
				'VALUE' => $arResult['NAME'] . ' ' . $arResult['LAST_NAME'] . ' (' . $arResult['EMAIL'] . ')'
			];
		}

		return $rsEnum;
	}
	//Получить HTML формы для редактирования свойства
	public static  function GetEditFormHTML($arUserField, $arHtmlControl)
	{
		if (($arUserField['ENTITY_VALUE_ID'] < 1) && strlen($arUserField['SETTINGS']['DEFAULT_VALUE']) > 0)
			$arHtmlControl['VALUE'] = intval($arUserField['SETTINGS']['DEFAULT_VALUE']);

		$result = '';
		$rsEnum = self::GetList($arUserField);
		if (!$rsEnum)
			return '';

		$bWasSelect = false;
		$result2 = '';
		foreach ($rsEnum as $arEnum) {
			$bSelected = (
				($arHtmlControl['VALUE'] == $arEnum['ID']) ||
				($arUserField['ENTITY_VALUE_ID'] <= 0 && $arEnum['DEF'] == 'Y') //Можно сделать логику для дефолтного значения
			);
			$bWasSelect = $bWasSelect || $bSelected;
			$result2 .= '<option value="' . $arEnum['ID'] . '"' . ($bSelected ? ' selected' : '') . '>' . $arEnum['VALUE'] . '</option>';
		}

		if ($arUserField["SETTINGS"]["LIST_HEIGHT"] > 1) {
			$size = ' size="' . $arUserField['SETTINGS']['LIST_HEIGHT'] . '"';
		} else {
			$arHtmlControl['VALIGN'] = 'middle';
			$size = '';
		}

		$result = '<select name="' . $arHtmlControl['NAME'] . '"' . $size . ($arUserField['EDIT_IN_LIST'] != "Y" ? ' disabled="disabled" ' : '') . '>';
		if ($arUserField["MANDATORY"] != "Y") {
			$result .= '<option value=""' . (!$bWasSelect ? ' selected' : '') . '>' . htmlspecialcharsbx(self::getEmptyCaption($arUserField)) . '</option>';
		}
		$result .= $result2;
		$result .= '</select>';

		return $result;
	}
	//Получить HTML формы для редактирования МНОЖЕСТВЕННОГО свойства
	public static  function GetEditFormHTMLMulty($arUserField, $arHtmlControl)
	{

		$rsEnum = self::GetList($arUserField);

		if (!$rsEnum) {
			return '';
		}

		$result = '<select multiple name="' . $arHtmlControl["NAME"] . '" size="' . $arUserField["SETTINGS"]["LIST_HEIGHT"] . '"' . ($arUserField["EDIT_IN_LIST"] != "Y" ? ' disabled="disabled" ' : '') . '>';

		if ($arUserField["MANDATORY"] <> "Y") {
			$result .= '<option value=""' . (!$arHtmlControl["VALUE"] ? ' selected' : '') . '>' . htmlspecialcharsbx(self::getEmptyCaption($arUserField)) . '</option>';
		}
		foreach ($rsEnum as $arEnum) {
			$bSelected = (
				(in_array($arEnum['ID'], $arHtmlControl["VALUE"])) ||
				($arUserField['ENTITY_VALUE_ID'] <= 0 && $arEnum['DEF'] == 'Y') //Можно сделать логику для дефолтного значения
			);
			$result .= '<option value="' . $arEnum["ID"] . '"' . ($bSelected ? ' selected' : '') . '>' . $arEnum["VALUE"] . '</option>';
		}
		$result .= '</select>';

		return $result;
	}
	//Получаем HTML для списка элементов в админке
	public static function GetAdminListViewHTML($arUserField, $arHtmlControl)
	{
		static $cache = array();
		$empty_caption = '&nbsp;';
		$rsEnum = '';
		if (!array_key_exists($arHtmlControl['VALUE'], $cache)) {
			$rsEnum = self::GetList($arUserField);
			if (!$rsEnum)
				return $empty_caption;

			foreach ($rsEnum as $arEnum) {
				$cache[$arEnum["ID"]] = $arEnum["VALUE"];
			}
		}
		if (!array_key_exists($arHtmlControl["VALUE"], $cache))
			$cache[$arHtmlControl["VALUE"]] = $empty_caption;
		return $cache[$arHtmlControl["VALUE"]];
	}
	//Получаем текст для пустого значения свойства
	protected static function getEmptyCaption($arUserField)
	{
		return $arUserField["SETTINGS"]["CAPTION_NO_VALUE"] <> ''
			? $arUserField["SETTINGS"]["CAPTION_NO_VALUE"]
			: 'Пользователь не выбран';
	}
	//Получить HTML для редактирования свойства в списке админ-панели
	public static function GetAdminListEditHTML($arUserField, $arHtmlControl)
	{

		$rsEnum = self::GetList($arUserField);
		if (!$rsEnum)
			return '';

		if ($arUserField["SETTINGS"]["LIST_HEIGHT"] > 1)
			$size = ' size="' . $arUserField["SETTINGS"]["LIST_HEIGHT"] . '"';
		else
			$size = '';

		$result = '<select name="' . $arHtmlControl["NAME"] . '"' . $size . ($arUserField["EDIT_IN_LIST"] != "Y" ? ' disabled="disabled" ' : '') . '>';
		if ($arUserField["MANDATORY"] != "Y") {
			$result .= '<option value=""' . (!$arHtmlControl["VALUE"] ? ' selected' : '') . '>' . htmlspecialcharsbx(self::getEmptyCaption($arUserField)) . '</option>';
		}
		foreach ($rsEnum as $key => $arEnum) {
			$result .= '<option value="' . $arEnum["ID"] . '"' . ($arHtmlControl["VALUE"] == $arEnum["ID"] ? ' selected' : '') . '>' . $arEnum["VALUE"] . '</option>';
		}
		$result .= '</select>';

		return $result;
	}
	//Получить HTML для редактирования множественного свойства в списке админ-панели
	public static function GetAdminListEditHTMLMulty($arUserField, $arHtmlControl)
	{

		if (!is_array($arHtmlControl["VALUE"]))
			$arHtmlControl["VALUE"] = array();

		$rsEnum = self::GetList($arUserField);
		if (!$rsEnum)
			return '';

		$result = '<select multiple name="' . $arHtmlControl["NAME"] . '" size="' . $arUserField["SETTINGS"]["LIST_HEIGHT"] . '"' . ($arUserField["EDIT_IN_LIST"] != "Y" ? ' disabled="disabled" ' : '') . '>';
		if ($arUserField["MANDATORY"] != "Y") {
			$result .= '<option value=""' . (!$arHtmlControl["VALUE"] ? ' selected' : '') . '>' . htmlspecialcharsbx(self::getEmptyCaption($arUserField)) . '</option>';
		}
		foreach ($rsEnum as $arEnum) {
			$result .= '<option value="' . $arEnum["ID"] . '"' . (in_array($arEnum["ID"], $arHtmlControl["VALUE"]) ? ' selected' : '') . '>' . $arEnum["VALUE"] . '</option>';
		}
		$result .= '</select>';
		return $result;
	}

	public static function GetDBColumnType()
	{
		global $DB;
		switch (strtolower($DB->type)) {
			case "mysql":
				return "int(18)";
			case "oracle":
				return "number(18)";
			case "mssql":
				return "int";
		}
	}
}
