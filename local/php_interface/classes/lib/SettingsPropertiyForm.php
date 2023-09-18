<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/*
 * Пояснения:
 * (*)  - Мы принимаем массив array('VALUE' => , 'DESCRIPTION' => ) и должны его же вернуть. Если поле с описанием - оно будет содержаться в соответствующем ключе.
 */

class SettingsPropertiyForm
{
   // инициализация пользовательского свойства для инфоблока
   public static function GetUserTypeDescription()
   {
      return array(
         "PROPERTY_TYPE" => "S", // основываемся на привязке к элементам
         "USER_TYPE" => "SettingsPropertiyForm",
         "DESCRIPTION" => "Строка для форм",
         'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
         "ConvertToDB" => array(__CLASS__, "ConvertToDB"),
         "ConvertFromDB" => array(__CLASS__, "ConvertFromDB"),
         "PrepareSettings" => array(__CLASS__, "PrepareSettings"),
         "GetSettingsHTML" => array(__CLASS__, "GetSettingsHTML"),
      );
   }

   public static function GetPropertyFieldHtml($arProperty = false, $value = false, $strHTMLControlName = false)
   {
      return '<input type="text" name="' . $strHTMLControlName["VALUE"] . '" value="' . $value['VALUE'] . '">';
   }

   public static function GetSettingsHTML($arProperty = false, $strHTMLControlName = false, &$arPropertyFields = false)
   {
      $arPropertyFields = array(
         "HIDE" => array("SMART_FILTER"), //will hide the field
         "SET" => array('FILTRABLE' => 'N', 'SEARCHABLE' => 'N'), //if set then hidden field will get this value
         "USER_TYPE_SETTINGS_TITLE" => "Дополнительный настройки"
      );

      $hidden = false;
      if ($arProperty["USER_TYPE_SETTINGS"]["HIDDEN"])
         $hidden = true;

      $phone = false;
      if ($arProperty["USER_TYPE_SETTINGS"]["PHONE"])
         $phone = true;
      $mail = false;
      if ($arProperty["USER_TYPE_SETTINGS"]["MAIL"])
         $mail = true;

      return '
        <tr valign="top">
            <td>Скрытое поле:</td>
            <td><input type="checkbox" name="' . $strHTMLControlName["NAME"] . '[HIDDEN]" ' . ($hidden ? 'checked="checked"' : '') . '></td>
        </tr>
        <tr valign="top">
            <td>Телефон:</td>
            <td><input type="checkbox" name="' . $strHTMLControlName["NAME"] . '[PHONE]" ' . ($phone ? 'checked="checked"' : '') . '></td>
        </tr>
        <tr valign="top">
            <td>Почта:</td>
            <td><input type="checkbox" name="' . $strHTMLControlName["NAME"] . '[MAIL]" ' . ($mail ? 'checked="checked"' : '') . '></td>
        </tr>
        ';
   }

   public static function PrepareSettings($arProperty = false)
   {



      if (isset($arProperty["USER_TYPE_SETTINGS"]["HIDDEN"]) || isset($arProperty["USER_TYPE_SETTINGS"]["PHONE"]) || isset($arProperty["USER_TYPE_SETTINGS"]["MAIL"])) {

         $returnArr = [];

         if (isset($arProperty["USER_TYPE_SETTINGS"]["HIDDEN"])) {

            $returnArr["HIDDEN"] = true;
         }

         if (isset($arProperty["USER_TYPE_SETTINGS"]["PHONE"])) {

            $returnArr["PHONE"] = true;
         }

         if (isset($arProperty["USER_TYPE_SETTINGS"]["MAIL"])) {

            $returnArr["MAIL"] = true;
         }
      }

      return $returnArr;
   }

   public static function GetAdminListViewHTML($arProperty = false, $value = false, $strHTMLControlName = false)
   {
      return;
   }

   public static function ConvertToDB($arProperty = false, $value = false) // сохранение в базу данных
   {
      return $value;
   }

   public static function ConvertFromDB($arProperty = false, $value = false) // извлечение значений из Базы Данных
   {
      return $value;
   }
}
