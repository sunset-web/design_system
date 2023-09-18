<?

// Класс реализует кастомный тип свойства инфоблока SEO свойство

class CIBlockNewPropertySEO

{

   // Обработчик свойства UpFly - SEO свойство

   static public function GetUserTypeDescription()

   {

      return array(

         "PROPERTY_TYPE"        => "S",

         "USER_TYPE"            => "upflySEO",

         "DESCRIPTION"          => "UpFly - SEO свойство",

         "GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),

         "ConvertToDB" => array(__CLASS__, "ConvertToDB"),

         "ConvertFromDB" => array(__CLASS__, "ConvertFromDB"),

      );
   }

   static public function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)

   {

      return;
   }

   // вывод поля свойства на странице редактирования одиночное

   static public function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)

   {

      // получить список полей шаблона для элемента

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

      // Наследование свойств - рекурсия

      $text = $value['VALUE'];

      if (!$text) {

         // Получаем список всех секций

         $Sections = array();

         $db_list = CIBlockSection::GetList(array("SORT" => "­­ASC"), array("IBLOCK_ID" => $arProperty['IBLOCK_ID']), false, array('UF_' . mb_strtoupper($arProperty["CODE"])));

         while ($ar_result = $db_list->GetNext()) {

            $Sections[$ar_result['ID']] = array('VALUE' => $ar_result['UF_' . mb_strtoupper($arProperty["CODE"])], 'PARENT' => $ar_result['IBLOCK_SECTION_ID']);
         }

         // Функция - рекурсия

         function seachValue($SectionId, $Sections, $default)

         {

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

         $text = seachValue($_REQUEST['find_section_section'], $Sections, $arProperty['DEFAULT_VALUE']);
      }

      // Проверка на дефолтное редактирование свойства и вывод нужной верстки

      if ($arProperty['FEATURES']) {

         $html .= '<input type="hidden" name="PROPERTY_DEFAULT_VALUE" id="result_PROP_' . $arProperty['ID'] . '_1" value="' . $value['VALUE'] . '"><textarea onclick="InheritedPropertiesTemplates.enableTextArea(\'PROP_' . $arProperty['ID'] . '\')" name="IPROPERTY_TEMPLATES[' . $arProperty['ID'] . '][TEMPLATE]" id="PROP_' . $arProperty['ID'] . '" cols="40" rows="1" style="width:80%">' . $value['VALUE'];

         $html .= '</textarea><input style="float:right" type="button" id="mnu_PROP_' . $arProperty['ID'] . '" value="..." class="adm-btn-active"><br><input type="hidden" name="IPROPERTY_TEMPLATES[' . $arProperty['ID'] . '][INHERITED]" value="Y"><input type="checkbox" name="IPROPERTY_TEMPLATES[' . $arProperty['ID'] . '][INHERITED]" id="ck_PROP_' . $arProperty['ID'] . '" value="Y" checked="checked" onclick="InheritedPropertiesTemplates.updateInheritedPropertiesTemplates()" class="adm-designed-checkbox">';
      } else {

         $html .= '<input type="hidden" name="PROP[' . $arProperty['ID'] . '][VALUE]" id="result_PROP_' . $arProperty['ID'] . '_1" value="' . $value['VALUE'] . '"><textarea onclick="InheritedPropertiesTemplates.enableTextArea(\'PROP_' . $arProperty['ID'] . '\')" name="IPROPERTY_TEMPLATES[' . $arProperty['ID'] . '][TEMPLATE]" id="PROP_' . $arProperty['ID'] . '" cols="55" rows="1" style="width:90%">' . $text;

         $html .= '</textarea><input style="float:right" type="button" id="mnu_PROP_' . $arProperty['ID'] . '" disabled value="..." class="adm-btn-active"><br><input type="hidden" name="IPROPERTY_TEMPLATES[' . $arProperty['ID'] . '][INHERITED]" value="Y"><input type="checkbox" name="IPROPERTY_TEMPLATES[' . $arProperty['ID'] . '][INHERITED]" id="ck_PROP_' . $arProperty['ID'] . '" value="N" ' . ($value['VALUE'] ? 'checked="checked"' : '') . ' onclick="InheritedPropertiesTemplates.updateInheritedPropertiesTemplates()" class="adm-designed-checkbox"><label class="adm-designed-checkbox-label" for="ck_PROP_' . $arProperty['ID'] . '" title=""></label><label for="ck_PROP_' . $arProperty['ID'] . '">Изменить для этого элемента.</label><br>';

         $html .= '<b><div id="result_PROP_' . $arProperty['ID'] . '"></div></b>';
      }

      return  $html;
   }

   // Сохранить свойство

   static public function ConvertToDB($arProperty, $value)

   {

      return $value;
   }

   // Получить свойство

   static public function ConvertFromDB($arProperty, $value)

   {

      return $value;
   }
}
