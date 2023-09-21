<?php

/*
 * Пояснения:
 * (*)  - Мы принимаем массив array('VALUE' => , 'DESCRIPTION' => ) и должны его же вернуть. Если поле с описанием - оно будет содержаться в соответствующем ключе.
 */

class UpflyCrossSell
{
   // инициализация пользовательского свойства для инфоблока
   public static function GetUserTypeDescription()
   {
      return array(
         "PROPERTY_TYPE" => "L", // основываемся на привязке к элементам
         "USER_TYPE" => "UpflyCrossSell",
         "DESCRIPTION" => "Upfly - перекрестные продажи",
         'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
         "ConvertToDB" => array(__CLASS__, "ConvertToDB"),
         "ConvertFromDB" => array(__CLASS__, "ConvertFromDB"),
      );
   }

   public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
   {
      $par = array(
         "parentContainer" => "limit_cond_container_PRODUCTS_FILTER_lfrlf",
         "form" => "",
         "formName" => "limit_cond_form_PRODUCTS_FILTER_lfrlf",
         "sepID" => "__",
         "prefix" => "rule"
      );
      $html = "";
      $html = '<script type="text/javascript" src="/bitrix/js/upfly/customfilter_control.js"></script>';
      $html .= '<link type="text/css" rel="stylesheet" href="/bitrix/panel/catalog/catalog_cond.css">';
      $html .= '<script src="https://code.jquery.com/jquery-3.6.0.min.js"  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="  crossorigin="anonymous"></script>';
      $html .= '<script type="text/javascript">
        BX.ready(function(){
            //var a = new BX.TreeCondCtrlAtom(' . CUtil::PhpToJSObject($par, false, true) . ');
            console.log(' . CUtil::PhpToJSObject($par, false, true) . ');
        
        });
        </script>';
      // получаем список типов цен
      $dbPriceType = CCatalogGroup::GetList(
         array("SORT" => "ASC"),
         array()
      );
      while ($arPriceType = $dbPriceType->Fetch()) {
         $prices_type[$arPriceType['ID']] = $arPriceType['NAME_LANG'];
      }

      //получаем список активных свойств инфоблока
      $properties = CIBlockProperty::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $arProperty['LINK_IBLOCK_ID']));
      while ($prop_fields = $properties->GetNext()) {
         $props[$prop_fields['ID']] = $prop_fields["NAME"];
      }

      $html .= '
        <div class="condition">
        <select name="' . $strHTMLControlName["VALUE"] . '" id="' . $strHTMLControlName["VALUE"] . '" class="condition__select">
        	<option value="">(выберите условие)</option>';
      $sel = ($value["VALUE"] == "SECTION") ? ' selected' : '';
      $html .= '<option cond="condition__section" value="SECTION"' . $sel . '>Раздел</option>
        	<optgroup label="Цены">';
      foreach ($prices_type as $key => $type) {
         $sel = ($value["VALUE"] == 'PRICE' . $key) ? ' selected' : '';
         $html .= '<option cond="condition__price" value="PRICE' . $key . '"' . $sel . '>' . $type . ' [' . $key . ']</option>';
      }
      $html .=
         '</optgroup>
        	<optgroup label="Свойства">';
      foreach ($props as $key => $prop) {
         $sel = ($value["VALUE"] == 'PROP' . $key) ? ' selected' : '';
         $html .= '<option cond="condition__prop" value="PROP' . $key . '"' . $sel . '>' . $prop . ' [' . $key . ']</option>';
      }
      $html .=
         '</optgroup>'
         . '</select>';
      $html .= '<span class="condition__action"></span>';
      $html .= '</div>';
      $html .= '&nbsp' . serialize($value);
      $html .= '<script type="text/javascript">'
         . '$("select.condition__select").on("change", function(){'
         . '	var action = "";'
         . '	switch ($(this).find("option:selected").attr("cond")) {'
         . '		case "condition__section":'
         . '			console.log("condition__section");'
         . '			break;'
         . '		case "condition__price":'
         . '			console.log("condition__price");'
         . '			break;'
         . '		case "condition__prop":'
         . '			console.log("condition__prop");'
         . '			action = "<select></select>"'
         . '			break;'
         . '		}'
         . '		$(this).parent().find(".condition__action").html();'
         . '				console.log($(this).find("option:selected").val());'
         . '			});'
         . '		</script>';

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
