<?
function getSettingField($field)
{
   if ($field) {
      return \Bitrix\Main\Config\Option::get("askaron.settings", $field);
   }
}
