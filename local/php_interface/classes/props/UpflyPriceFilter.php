<?php
AddEventHandler("iblock", "OnIBlockPropertyBuildList", array("UpflyPriceFilter", "GetUserTypeDescription"));
/*
 * Пояснения:
 * (*)  - Мы принимаем массив array('VALUE' => , 'DESCRIPTION' => ) и должны его же вернуть. Если поле с описанием - оно будет содержаться в соответствующем ключе.
 */

class UpflyPriceFilter
{
   // инициализация пользовательского свойства для инфоблока
   public static function GetUserTypeDescription()
   {
      return array(
         "PROPERTY_TYPE" => "E", // основываемся на привязке к элементам
         "USER_TYPE" => "UpflyPriceFilter",
         "DESCRIPTION" => "Upfly - привязка к цене",
         'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
         "ConvertToDB" => array(__CLASS__, "ConvertToDB"),
         "ConvertFromDB" => array(__CLASS__, "ConvertFromDB"),
      );
   }

   public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
   {
      $dbPriceType = CCatalogGroup::GetList(
         array("SORT" => "ASC"),
         array()
      );
      while ($arPriceType = $dbPriceType->Fetch()) {
         $prices_type[$arPriceType['ID']] = array($arPriceType['XML_ID'], $arPriceType['NAME_LANG']);
      }

      $html = '<select name="' . $strHTMLControlName["VALUE"] . '" id="' . $strHTMLControlName["VALUE"] . '">';
      $html .= '<option value="0">Не фильтровать</option>';
      foreach ($prices_type as $key => $type) {
         $sel = ($value["VALUE"] == $key) ? ' selected' : '';
         $html .= '<option value="' . $key . '"' . $sel . '>' . $type[1] . ' [' . $type[0] . ']</option>';
      }
      $html .= '</select>';

      return $html;
   }

   public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
   {
      return;
   }

   public static function ConvertToDB($arProperty, $value) // сохранение в базу данных
   {
      return $value;
   }

   public static function ConvertFromDB($arProperty, $value) // извлечение значений из Базы Данных
   {
      return $value;
   }
}
