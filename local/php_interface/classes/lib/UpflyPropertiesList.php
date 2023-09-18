<?

/*
 * Пояснения:
 * (*)  - Мы принимаем массив array('VALUE' => , 'DESCRIPTION' => ) и должны его же вернуть. Если поле с описанием - оно будет содержаться в соответствующем ключе.
 */

class UpflyPropertiesList
{
   // инициализация пользовательского свойства для инфоблока
   static function GetUserTypeDescription()
   {
      return array(
         "PROPERTY_TYPE" => "E", // основываемся на привязке к элементам
         "USER_TYPE" => "UpflyPropertiesList",
         "DESCRIPTION" => "Upfly - привязка к свойствам инфоблока",
         'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
         "ConvertToDB" => array(__CLASS__, "ConvertToDB"),
         "ConvertFromDB" => array(__CLASS__, "ConvertFromDB"),
      );
   }

   static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
   {
      $properties = CIBlockProperty::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $arProperty['LINK_IBLOCK_ID']));
      while ($prop_fields = $properties->GetNext()) {
         $props[$prop_fields['ID']] = $prop_fields;
      }

      $html = '<select name="' . $strHTMLControlName["VALUE"] . '" id="' . $strHTMLControlName["VALUE"] . '">';
      $html .= '<option></option>';

      foreach ($props as $key => $prop) {
         if ($value["VALUE"] == $key) {
            $sel = ' selected';
            $dump = $prop;
         } else {
            $sel = "";
         }

         $html .= '<option value="' . $key . '"' . $sel . '>' . $prop["NAME"] . ' [' . $key . ']</option>';
      }

      $html .= '</select>';
      $html .= '&nbsp&nbspЗначение: ';

      switch ($dump['PROPERTY_TYPE']) {
         case 'N':
         case 'S':
            $html .= '<input type="text" id="' . $strHTMLControlName["DESCRIPTION"] . '" name="' . $strHTMLControlName["DESCRIPTION"] . '" value="' . $value["DESCRIPTION"] . '">';
            break;
         case 'E':

            // получение информации по выбранному элементу
            if (intval($value["VALUE"]) > 0) {

               $res = \CIBlockElement::GetByID($value["DESCRIPTION"]);
               if ($ar_res = $res->GetNext())
                  $ItemName = $ar_res['NAME'];
            }
            $html .= '<input type="text" id="' . $strHTMLControlName["DESCRIPTION"] . '" name="' . $strHTMLControlName["DESCRIPTION"] . '" value="' . $value["DESCRIPTION"] . '">';
            $html .= '<input type="button" value="..." onclick="jsUtils.OpenWindow(\'/bitrix/tools/iblock/element_search.php?lang=' . LANG . '&IBLOCK_ID=' . $dump["LINK_IBLOCK_ID"] . '&n=' . $strHTMLControlName["DESCRIPTION"] . '&tableId=iblockprop-E-' . $dump["ID"] . '-' . $dump["LINK_IBLOCK_ID"] . '&iblockfix=y\', 900, 700);">&nbsp<span id="sp_' . md5($strHTMLControlName["DESCRIPTION"]) . '">' . $ItemName . '</span>';
            break;
         default:
            break;
      }

      return  $html;
   }

   static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
   {
      return;
   }

   static function ConvertToDB($arProperty, $value) // сохранение в базу данных
   {

      return $value;
   }

   static function ConvertFromDB($arProperty, $value) // извлечение значений из Базы Данных
   {

      return $value;
   }
}
