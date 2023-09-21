<?
// Класс реализует кастомный тип свойства инфоблока SEO свойство
class CIBlockNewPropertyStockSection extends CUserTypeEnum
{
	// Обработчик свойства UpFly - SEO свойство
    static function GetUserTypeDescription()
    {
        return array(
        	"USER_TYPE_ID" => "upflyStorage",
	         "CLASS_NAME" => __CLASS__,
	         "DESCRIPTION" => "UpFly - привязка к складам",
	         "BASE_TYPE" => "enum"
        );
    }
     // вывод поля свойства на странице редактирования одиночное
    function GetEditFormHTML($arProperty, $value)
    {
    	// получить список складов
		$rsData = CCatalogStore::GetList(array(), array(), false, false, array("TITLE","ID","ACTIVE"));
		$html = '<select size="1" name="' . $arProperty['FIELD_NAME'] . '[]">';
		$html .= '<option value="0">(не установлено)</option>';
		while ( $arStorage = $rsData->GetNext() ) {
			if($arStorage && $arStorage['ACTIVE'] == 'Y'){
				$html .= '<option'.($value['VALUE'] == $arStorage['ID'] ? ' selected' : '').' value="' . $arStorage['ID'] . '">' . $arStorage['TITLE'] . '</option>';
			};
		};
		$html .= '</select>';
		return  $html;
	}
	function getEditFormHtmlMulty($arProperty, $value)
    {
    	// получить список складов
		$rsData = CCatalogStore::GetList(array(), array(), false, false, array("TITLE","ID","ACTIVE"));
		$html = '<select size="5" name="' . $arProperty['FIELD_NAME'] . '[]" multiple>';
		$html .= '<option value="0">(не установлено)</option>';
		while ( $arStorage = $rsData->GetNext() ) {
			if($arStorage && $arStorage['ACTIVE'] == 'Y'){
				$html .= '<option'.(in_array($arStorage['ID'], $value['VALUE']) ? ' selected' : '').' value="' . $arStorage['ID'] . '">' . $arStorage['TITLE'] . '</option>';
			};
		};
		$html .= '</select>';
		return  $html;
	}
	static function GetDBColumnType()
   {
      global $DB;
      switch(strtolower($DB->type))
      {
         case "mysql":
            return "int(18)";
         case "oracle":
            return "number(18)";
         case "mssql":
            return "int";
      }
   }
	// // Сохранить свойство
	public function OnBeforeSave($arUserField, $value)
    {
            return $value;
    }
}
