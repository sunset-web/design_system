<?
AddEventHandler("main", "OnUserTypeBuildList", array("CIBlockNewPropertySectionSEO", "GetUserTypeDescription"));
// Класс реализует кастомный тип свойства инфоблока SEO свойство

class CIBlockNewPropertySectionSEO extends CUserTypeString

{

   // Обработчик свойства UpFly - SEO свойство

   static function GetUserTypeDescription()

   {

      return array(

         "USER_TYPE_ID" => "upflySEO",

         "CLASS_NAME" => __CLASS__,

         "DESCRIPTION" => "UpFly - SEO свойство",

         "BASE_TYPE" => "string"

      );
   }

   // вывод поля свойства на странице редактирования одиночное

   function GetEditFormHTML($arProperty, $value)

   {

      // получить список полей шаблона для раздела

      $menuItems = CIBlockParameters::GetInheritedPropertyTemplateElementMenuItems($arProperty['IBLOCK_ID'], "InheritedPropertiesTemplates.insertIntoInheritedPropertiesTemplate", "mnu_PROP_" . $arProperty['ID'], "PROP_" . $arProperty['ID']);

      $u = new CAdminPopupEx("mnu_PROP_" . $arProperty['ID'], $menuItems, array("zIndex" => 2000));

      // вывод верстки элемента

      $html = $u->Show(true) . '<script>

			window.ipropTemplates[window.ipropTemplates.length] = {

			"ID": "' . $arProperty['ID'] . '",

			"INPUT_ID": "PROP_' . $arProperty['ID'] . '",

			"RESULT_ID": "result_PROP_' . $arProperty['ID'] . '",

			"TEMPLATE": ""

			};

			BX.ready(function(){

			   BX.bind(BX(\'PROP_' . $arProperty['ID'] . '\'), \'change\', function() {

					BX(\'result_PROP_' . $arProperty['ID'] . '_1\').value = this.value;

				});

			});

		</script>';

      // Наследование свойств

      $text = $value['VALUE'];

      if (!$text) {

         // Получаем список всех секций

         $Sections = array();

         $db_list = CIBlockSection::GetList(array("SORT" => "­­ASC"), array("IBLOCK_ID" => $_REQUEST['IBLOCK_ID']), false, array($arProperty["FIELD_NAME"]));

         while ($ar_result = $db_list->GetNext()) {

            $Sections[$ar_result['ID']] = array('VALUE' => $ar_result[$arProperty["FIELD_NAME"]], 'PARENT' => $ar_result['IBLOCK_SECTION_ID']);
         }

         function seachValue($SectionId, $Sections, $default)

         {

            if ($SectionId == 0) return $default;

            if ($Sections[$SectionId]['VALUE']) {

               return $Sections[$SectionId]['VALUE'];
            } else {

               if ($Sections[$SectionId]['PARENT']) {

                  return seachValue($Sections[$SectionId]['PARENT'], $Sections, $default);
               } else {

                  return $default;
               }
            }
         }

         // Получаем свойство элемента

         $elDefaultVal = '';

         $res = CIBlockProperty::GetByID(str_replace("UF_", "", $arProperty["FIELD_NAME"]), $_REQUEST['IBLOCK_ID'], false);

         if ($ar_res = $res->GetNext())

            $elDefaultVal = $ar_res['DEFAULT_VALUE'];



         $text = seachValue($_REQUEST['find_section_section'], $Sections, $elDefaultVal);
      }



      $html .= '<input type="hidden" name="' . $arProperty['FIELD_NAME'] . '" id="result_PROP_' . $arProperty['ID'] . '_1" value="' . $value['VALUE'] . '"><textarea onclick="InheritedPropertiesTemplates.enableTextArea(\'PROP_' . $arProperty['ID'] . '\')" name="IPROPERTY_TEMPLATES[' . $arProperty['ID'] . '][TEMPLATE]" id="PROP_' . $arProperty['ID'] . '" cols="55" rows="1" style="width:90%">' . $text;

      $html .= '</textarea><input style="float:right" type="button" id="mnu_PROP_' . $arProperty['ID'] . '" disabled value="..." class="adm-btn-active"><br><input type="hidden" name="IPROPERTY_TEMPLATES[' . $arProperty['ID'] . '][INHERITED]" value="Y"><input type="checkbox" name="IPROPERTY_TEMPLATES[' . $arProperty['ID'] . '][INHERITED]" id="ck_PROP_' . $arProperty['ID'] . '" value="N" ' . ($value['VALUE'] ? 'checked="checked"' : '') . ' onclick="InheritedPropertiesTemplates.updateInheritedPropertiesTemplates()" class="adm-designed-checkbox"><label class="adm-designed-checkbox-label" for="ck_PROP_' . $arProperty['ID'] . '" title=""></label><label for="ck_PROP_' . $arProperty['ID'] . '">Изменить для этого элемента.</label><br>';

      $html .= '<b><div id="result_PROP_' . $arProperty['ID'] . '"></div></b>';

      return  $html;
   }

   static function GetDBColumnType()

   {

      global $DB;

      switch (strtolower($DB->type)) {

         case "mysql":

            return "text";

         case "oracle":

            return "varchar2(2000 char)";

         case "mssql":

            return "varchar(2000)";
      }
   }

   // Сохранить свойство

   function OnBeforeSave($arUserField, $value)

   {

      return $value;
   }
}
