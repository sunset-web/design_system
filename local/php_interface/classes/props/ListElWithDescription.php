<?
AddEventHandler("iblock", "OnIBlockPropertyBuildList", array("ListElWithDescription", "GetUserTypeDescription"));
/*
 * Пояснения:
 * (*)  - Мы принимаем массив array('VALUE' => , 'DESCRIPTION' => ) и должны его же вернуть. Если поле с описанием - оно будет содержаться в соответствующем ключе.
 */

class ListElWithDescription
{
    // инициализация пользовательского свойства для инфоблока
    function GetUserTypeDescription()
    {
        return array(
            "PROPERTY_TYPE" => "E", // основываемся на привязке к элементам
            "USER_TYPE" => "upflyListElDescription",
            "DESCRIPTION" => "Upfly - Привязка к элементам с доп.описанием",
            'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
            "ConvertToDB" => array(__CLASS__, "ConvertToDB"),
            "ConvertFromDB" => array(__CLASS__, "ConvertFromDB"),
        );
    }

    function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        //$value["DESCRIPTION"] = unserialize($value["DESCRIPTION"]);

        // значения по умолчанию
        $arItem = array(
            "ID" => 0,
            "IBLOCK_ID" => 0,
            "NAME" => ""
        );

        // получение информации по выбранному элементу
        if (intval($value["VALUE"]) > 0) {
            $arFilter = array(
                "ID" => intval($value["VALUE"]),
                "IBLOCK_ID" => $arProperty["LINK_IBLOCK_ID"],
            );

            $arItem = \CIBlockElement::GetList(array(), $arFilter, false, false, array("ID", "IBLOCK_ID", "NAME"))->Fetch();
        }

        // сама строка с товаром и доп.значениями

        $html =
            '<input name="' . $strHTMLControlName["VALUE"] . '" id="' . $strHTMLControlName["VALUE"] . '" value="' . $value["VALUE"] . '" size="5" type="text">' .
            ' <span id="sp_' . md5($strHTMLControlName["VALUE"]) . '"="">' . $arItem["NAME"] . '</span>   ' .
            // '<input type="button" value="..." onclick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_element_search.php?lang='.LANG.'&IBLOCK_ID='.$arProperty["LINK_IBLOCK_ID"].'&n='.$strHTMLControlName["VALUE"].'\', 900, 500);">'.
            '<input type="button" value="..." onclick="jsUtils.OpenWindow(\'/bitrix/tools/iblock/element_search.php?lang=' . LANG . '&IBLOCK_ID=' . $arProperty["LINK_IBLOCK_ID"] . '&n=' . $strHTMLControlName["VALUE"] . '&tableId=iblockprop-E-' . $arProperty["ID"] . '-' . $arProperty["LINK_IBLOCK_ID"] . '&iblockfix=y\', 900, 700);">' .
            ' Дополнение:<input type="text" id="' . $strHTMLControlName["DESCRIPTION"] . '" name="' . $strHTMLControlName["DESCRIPTION"] . '" value="' . $value["DESCRIPTION"] . '">';
        //$html .= serialize($strHTMLControlName);

        return  $html;
    }

    function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        return;
    }

    function ConvertToDB($arProperty, $value) // сохранение в базу данных
    {
        /*$return = false;
         
        if( is_array($value) && array_key_exists("VALUE", $value) )
        {
            $return = array(
                "VALUE" => serialize($value["VALUE"])
            );
        }*/

        // сериализацию убирать не стал, если понадобится сохранять несколько значений
        /* if( is_array($value) && array_key_exists("DESCRIPTION", $value) )
            $return["DESCRIPTION"] = $value["DESCRIPTION"];*/

        return $value;
    }

    function ConvertFromDB($arProperty, $value) // извлечение значений из Базы Данных
    {
        /*$return = false;
         
        if(!is_array($value["VALUE"]))
        {
            $return = array(
                "VALUE" => unserialize($value["VALUE"])
            );

            $return['DESCRIPTION'] = unserialize($value["DESCRIPTION"]);
        }  */

        return $value;
    }
}
