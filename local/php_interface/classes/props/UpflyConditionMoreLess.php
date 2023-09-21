<?php

/*
 * Пояснения:
 * (*)  - Мы принимаем массив array('VALUE' => , 'DESCRIPTION' => ) и должны его же вернуть. Если поле с описанием - оно будет содержаться в соответствующем ключе.
 */

class UpflyConditionMoreLess
{
   // инициализация пользовательского свойства для инфоблока
   public static function GetUserTypeDescription()
   {
      return array(
         "PROPERTY_TYPE" => "S", // основываемся на привязке к элементам
         "USER_TYPE" => "UpflyConditionMoreLess",
         "DESCRIPTION" => "Upfly - условие фильтрации (больше/меньше)",
         'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
         'GetAdminListViewHTML' => array(__CLASS__, 'GetAdminListViewHTML'),
         "ConvertToDB" => array(__CLASS__, "ConvertToDB"),
         "ConvertFromDB" => array(__CLASS__, "ConvertFromDB"),
      );
   }

   public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
   {
      $values = array(
         //'none' => 'Выберите условие',
         'm' => 'Больше',
         'l' => 'Меньше',
         'e' => 'Равно',
         'me' => 'Больше или равно',
         'le' => 'Меньше или равно',
      );

      $html = '<select name="' . $strHTMLControlName["VALUE"] . '" id="' . $strHTMLControlName["VALUE"] . '">';
      $html .= '<option></option>';
      foreach ($values as $key => $type) {
         $sel = ($value["VALUE"] == $key) ? ' selected' : '';
         $html .= '<option value="' . $key . '"' . $sel . '>' . $type . '</option>';
      }
      $html .= '</select>';
      $html .= '&nbspЗначение:&nbsp<input type="text" id="' . $strHTMLControlName["DESCRIPTION"] . '" name="' . $strHTMLControlName["DESCRIPTION"] . '" value="' . $value["DESCRIPTION"] . '">';

      return $html;
   }

   public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
   {
      return serialize($arProperty);
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
