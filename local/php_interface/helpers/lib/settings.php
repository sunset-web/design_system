<?

/**
 * Возвращает значение параметра
 * @param $field - имя параметра
 * @return string 
 */
function getSettingField($field)
{
   if ($field) {
      return \Bitrix\Main\Config\Option::get("askaron.settings", $field);
   }
}