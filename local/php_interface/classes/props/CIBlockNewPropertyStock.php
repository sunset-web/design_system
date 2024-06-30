<?
AddEventHandler("iblock", "OnIBlockPropertyBuildList", array("CIBlockNewPropertyStock", "GetUserTypeDescription"));
// Класс реализует кастомный тип свойства инфоблока привязка к складам
class CIBlockNewPropertyStock
{
	// Обработчик свойства UpFly - привязка к складам
	function GetUserTypeDescription()
	{
		return array(
			"PROPERTY_TYPE"        => "S",
			"USER_TYPE"            => "upflyStorage",
			"DESCRIPTION"          => "UpFly - привязка к складам",
			"GetPropertyFieldHtmlMulty" => array(__CLASS__, "GetPropertyFieldHtmlMulty"),
			"GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
			"ConvertToDB" => array(__CLASS__, "ConvertToDB"),
			"ConvertFromDB" => array(__CLASS__, "ConvertFromDB"),
		);
	}
	function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
	{
		return;
	}
	// вывод поля свойства на странице редактирования одиночное
	function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		// получить список складов
		$rsData = CCatalogStore::GetList(array(), array(), false, false, array("TITLE", "ID", "ACTIVE"));
		$html = '<select size="1" name="PROP[' . $arProperty['ID'] . '][]">';
		$html .= '<option value="0">(не установлено)</option>';
		while ($arStorage = $rsData->GetNext()) {
			if ($arStorage && $arStorage['ACTIVE'] == 'Y') {
				$html .= '<option' . ($value['VALUE'] == $arStorage['ID'] ? ' selected' : '') . ' value="' . $arStorage['ID'] . '">' . $arStorage['TITLE'] . '</option>';
			};
		};
		$html .= '</select>';
		return  $html;
	}
	// вывод поля свойства на странице редактирования множественное
	function GetPropertyFieldHtmlMulty($arProperty, $value, $strHTMLControlName)
	{
		// получить список складов
		$rsData = CCatalogStore::GetList(array(), array(), false, false, array("TITLE", "ID", "ACTIVE"));
		$html = '<select size="5" name="' . $strHTMLControlName['VALUE'] . '[]" multiple="multiple">';
		$html .= '<option value="0">(не установлено)</option>';
		while ($arStorage = $rsData->GetNext()) {
			if ($arStorage && $arStorage['ACTIVE'] == 'Y') {
				$html .= '<option' . (in_array($arStorage['ID'], array_column($value, 'VALUE')) ? ' selected' : '') . ' value="' . $arStorage['ID'] . '">' . $arStorage['TITLE'] . '</option>';
			};
		};
		$html .= '</select>';
		return  $html;
	}
	// Сохранить свойство
	function ConvertToDB($arProperty, $value)
	{
		return $value;
	}
	// Получить свойство
	function ConvertFromDB($arProperty, $value)
	{
		return $value;
	}
}
