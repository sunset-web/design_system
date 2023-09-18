<?
AddEventHandler("search", "BeforeIndex", array("SearchCatalog", "BeforeIndexHandler"));
class SearchCatalog
{
   // метод модифицирует поисковый индекс для элементов и разделов инфоблока

   static function BeforeIndexHandler($arFields)
   {
      $IBLOCK_ID = 'your_id';

      // Обрабатываем только нужный инфоблок
      if ($arFields["MODULE_ID"] == "iblock" && $arFields["PARAM2"] == $IBLOCK_ID) {
         $arFields["PARAMS"]["iblock_section"] = array();

         // Добавляем разделы элемента с учетом родительских разделов

         if (substr($arFields["ITEM_ID"], 0, 1) != "S") {
            // Получаем разделы привязки элемента (их может быть несколько)
            $rsSections = CIBlockElement::GetElementGroups($arFields["ITEM_ID"], true);
            while ($arSection = $rsSections->Fetch()) {
               $nav = CIBlockSection::GetNavChain($IBLOCK_ID, $arSection["ID"]);
               while ($ar = $nav->Fetch()) {
                  //Сохраняем в поисковый индекс
                  $arFields["PARAMS"]["iblock_section"][] = $ar['ID'];
               }
            }
         }
         // Добавляем разделы раздела с учетом родительских разделов
         else {
            // Получаем разделы
            $nav = CIBlockSection::GetNavChain($IBLOCK_ID, substr($arFields["ITEM_ID"], 1, strlen($arFields["ITEM_ID"])));
            while ($ar = $nav->Fetch()) {
               //Сохраняем в поисковый индекс
               $arFields["PARAMS"]["iblock_section"][] = $ar['ID'];
            }
         }
      }
      //Всегда возвращаем arFields
      return $arFields;
   }
}
