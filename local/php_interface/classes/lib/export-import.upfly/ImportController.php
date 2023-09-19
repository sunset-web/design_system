<?
// Класс реализует загрузку файла, создание элементов в базе данных
class ImportController
{
   // инициализация входных данных

   private static $IBLOCK_ID = 2;

   private static $FULL_PATH_FOLDER = '/upload/import.upfly/s';

   private static $FULL_FILE_PATH;

   function __construct(string $SECTION_ID)
   {

      $this->DOCUMENT_ROOT = \Bitrix\Main\Application::getDocumentRoot();

      self::$FULL_PATH_FOLDER = $this->DOCUMENT_ROOT . self::$FULL_PATH_FOLDER . $SECTION_ID;
   }

   // Проверяет на наличие папки и создает её
   private function checkFolder(string $dir)
   {
      if (!is_dir($dir)) {
         mkdir($dir, 0777, true);

         return true;
      }

      return false;
   }

   // Загружает файл
   public function loadFile(array $file)
   {
      self::checkFolder(self::$FULL_PATH_FOLDER);

      // Преобразуем путь
      self::$FULL_FILE_PATH = self::$FULL_PATH_FOLDER . '/' . $file['name'];

      // Проверяем наличие файла, есть => удаляем
      if (file_exists(self::$FULL_FILE_PATH)) {

         \Bitrix\Main\IO\File::deleteFile(self::$FULL_FILE_PATH);
      }
      // Переносим файл
      move_uploaded_file($file['tmp_name'], self::$FULL_FILE_PATH);

      return self::$FULL_FILE_PATH;
   }
   // Получение справочника свойства
   protected function getPropInfo(string $code)
   {

      $queryObj = CIBlockPropertyEnum::GetList([], [
         "IBLOCK_ID" => self::$IBLOCK_ID,
         "CODE" => $code,
      ]);
      while ($res = $queryObj->Fetch()) {

         $list[$res['XML_ID']]['ID'] = $res['ID'];
         $list[$res['XML_ID']]['VALUE'] = mb_strtolower($res['VALUE']);
         $list[$res['XML_ID']]['XML_ID'] = $res['XML_ID'];
      }

      return $list;
   }

   // Разбираем файл, подготавливаем массивы
   public static function parseFile(string $file, string $sectionID)
   {
      // Множественный разделитель
      $separator = '|';
      // Символ наличия
      $acceptSymbol = '✓';
      //Справочная информация
      \Bitrix\Main\Loader::includeModule("iblock");
      $ageArray = self::getPropInfo('AGE');
      $categoriesArray = self::getPropInfo('CATEGORIES');
      $typeArray = self::getPropInfo('TYPE');
      $measureParamArray = self::getPropInfo('MEASURE');
      // Формат продажи
      $measureArray = [
         0 => [
            'ID' => 3,
            'VALUE' => 'Граммы',
            'DEFAULT' => '50',
         ],
         1 => [
            'ID' => 2,
            'VALUE' => 'Литры',
            'DEFAULT' => '0.5',
         ],
         2 => [
            'ID' => 5,
            'VALUE' => 'Штучно',
            'DEFAULT' => '1',
         ],
      ];


      // Сопоставления полей и столбцов
      $arComparison = [
         //список
         5 => 'AGE',
         8 => 'CATEGORIES',
         10 => 'MEASURE',
         23 => 'TYPE',
         // фото
         12 => 'PHOTOS',
         // текст
         1 => 'COMPOUND',
         13 => 'STORAGE_CONDITIONS',
         // строчные
         4 => 'CML2_ARTICLE',
         7 => 'OLD_PRICE',
         11 => 'VOLUME',
         14 => 'CALORIES',
         15 => 'PROTEINS',
         16 => 'FATS',
         17 => 'CARBOHYDRATES',
         18 => 'BRAND',
         19 => 'COUNTRY',
         20 => 'FORTRESS',
         21 => 'COLOR',
         22 => 'DENSITY',
         24 => 'VIEW',
         25 => 'TASTE',
         26 => 'MANUFACTURER',

      ];

      \Bitrix\Main\Loader::includeModule("nkhost.phpexcel");


      global $PHPEXCELPATH;

      require_once($PHPEXCELPATH . '/PHPExcel/IOFactory.php');

      $xls = PHPExcel_IOFactory::load($file);
      // Устанавливаем индекс активного листа
      $xls->setActiveSheetIndex(1);
      // Получаем активный лист
      $sheet = $xls->getActiveSheet();

      for ($i = 2; $i <= $sheet->getHighestRow(); $i++) {
         $nColumn = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());

         $measureCount;

         for ($j = 0; $j < $nColumn; $j++) {

            $val = trim(strip_tags($sheet->getCellByColumnAndRow($j, $i)->getValue()));
            switch ($j) {
               case 0: //Наименование
                  $arProducts['write'][$i]['NAME'] = $arProducts['view'][$i]['NAME'] = $val;
                  $arProducts['write'][$i]['ACTIVE'] = 'Y';
                  $arProducts['write'][$i]['IBLOCK_SECTION_ID'] = $sectionID;
                  $arProducts['write'][$i]['IBLOCK_ID'] = self::$IBLOCK_ID;
                  break;
               case 2: //Описание
                  $arProducts['write'][$i]['DETAIL_TEXT'] = $arProducts['view'][$i]['DETAIL_TEXT'] = $val;
                  break;
               case 3: //Ссылка на изображение
                  $arProducts['write'][$i]['DETAIL_PICTURE'] = $arProducts['write'][$i]['PREVIEW_PICTURE'] = $val;

                  break;
               case 5: //18+
                  $arProducts['write'][$i]['PROPERTY_VALUES'][$arComparison[$j]]['VALUE'] = $ageArray[mb_strtolower($arComparison[$j]) . $val]['ID'];
                  $arProducts['view'][$i]['PROPERTY_VALUES'][$arComparison[$j]] = ($val ? $acceptSymbol : '');
                  break;
               case 8: //Категория
                  $categoriesArr = explode($separator, $val);
                  foreach ($categoriesArr as $key => $category) {
                     $category = trim($category);
                     $arProducts['view'][$i]['PROPERTY_VALUES'][$arComparison[$j]] .= $category . ', ';

                     $arProducts['write'][$i]['PROPERTY_VALUES'][$arComparison[$j]][$key] = $categoriesArray[array_search(mb_strtolower($category), array_column($categoriesArray, 'VALUE', 'XML_ID'))]['ID'];
                  }
                  $arProducts['view'][$i]['PROPERTY_VALUES'][$arComparison[$j]] = substr($arProducts['view'][$i]['PROPERTY_VALUES'][$arComparison[$j]], 0, -2);

                  $arProducts['write'][$i]['PROPERTY_VALUES'][$arComparison[$j]] = array_values(array_diff($arProducts['write'][$i]['PROPERTY_VALUES'][$arComparison[$j]], array('')));

                  break;
               case 23: //Тип
                  $typeArr = explode($separator, $val);
                  foreach ($typeArr as $key => $type) {
                     $type = trim($type);

                     $arProducts['write'][$i]['PROPERTY_VALUES'][$arComparison[$j]][$key] = $typeArray[array_search(mb_strtolower($type), array_column($typeArray, 'VALUE', 'XML_ID'))]['ID'];
                  }

                  $arProducts['write'][$i]['PROPERTY_VALUES'][$arComparison[$j]] = array_values(array_diff($arProducts['write'][$i]['PROPERTY_VALUES'][$arComparison[$j]], array('')));

                  break;
               case 6: //Цена
                  $arProducts['write'][$i]['PRICE'] = $arProducts['view'][$i]['PRICE'] = $val;
                  break;
               case 9: //Формат продажи
                  $arProducts['write'][$i]['MEASURE'] = $measureArray[$val]['ID'];
                  $arProducts['write'][$i]['MEASURE_DEFAULT'] = $measureArray[$val]['DEFAULT'];
                  $arProducts['view'][$i]['MEASURE'] = $measureArray[$val]['VALUE'];
                  $measureCount = $val;
                  break;
               case 10: //Параметр

                  if ($measureCount == 2) {

                     $arProducts['write'][$i]['PROPERTY_VALUES'][$arComparison[$j]]['VALUE'] = $measureParamArray[mb_strtolower($arComparison[$j]) . $val]['ID'];
                     $arProducts['view'][$i]['PROPERTY_VALUES'][$arComparison[$j]] = $measureParamArray[mb_strtolower($arComparison[$j]) . $val]['VALUE'];
                  }

                  break;
               case 11: //Значение параметра

                  if ($measureCount == 2) {

                     $arProducts['write'][$i]['PROPERTY_VALUES'][$arComparison[$j]] = $arProducts['view'][$i]['PROPERTY_VALUES'][$arComparison[$j]] = $val;
                  } else if ($measureCount == 1) {

                     $arProducts['write'][$i]['PROPERTY_VALUES'][$arComparison[$j]] = $arProducts['view'][$i]['PROPERTY_VALUES'][$arComparison[$j]] = $measureArray[$measureCount]['DEFAULT'] . ' л';
                  } else {
                     $arProducts['write'][$i]['PROPERTY_VALUES'][$arComparison[$j]] = $arProducts['view'][$i]['PROPERTY_VALUES'][$arComparison[$j]] = $measureArray[$measureCount]['DEFAULT'] . ' г';
                  }

                  break;
               case 12: //Дополнительные изображения
                  $imagesArr = explode($separator, $val);
                  foreach ($imagesArr as $key => $image) {
                     $arProducts['write'][$i]['PROPERTY_VALUES'][$arComparison[$j]][$key] = trim($image);
                  }
                  break;
                  //текст
               case 1: //Состав
               case 13: //Срок и условия хранения
                  $arProducts['write'][$i]['PROPERTY_VALUES'][$arComparison[$j]][0]['VALUE'] = ['TEXT' => $val, 'TYPE' => "TEXT"];
                  $arProducts['view'][$i]['PROPERTY_VALUES'][$arComparison[$j]] = $val;
                  break;
                  //строка
               case 4: //Артикул
               case 7: //Старая цена
               case 14: //Калории
               case 15: //Белки
               case 16: //Жиры
               case 17: //Углеводы
               case 18: //Бренд
               case 19: //Страна
               case 20: //Крепость
               case 21: //Цвет
               case 22: //Плотность
               case 24: //Вид
               case 25: //Вкус
               case 26: //Завод изготовитель
                  $arProducts['write'][$i]['PROPERTY_VALUES'][$arComparison[$j]] = $arProducts['view'][$i]['PROPERTY_VALUES'][$arComparison[$j]] = $val;
                  break;
            }
         }
      }

      return $arProducts;
   }
   // Проверка наличия элемента
   protected function checkElementField(string $propValue, string $sectionID)
   {

      $elementDB = \Bitrix\Iblock\ElementTable::getList([
         'filter' => [
            'prop.IBLOCK_PROPERTY_ID' => 79,
            'IBLOCK_ID' => self::$IBLOCK_ID,
            'IBLOCK_SECTION_ID' => $sectionID,
            'prop.VALUE' => $propValue,
         ],
         'runtime' => [
            new \Bitrix\Main\Entity\ReferenceField('prop', \Bitrix\Iblock\ElementPropertyTable::getEntity(), [
               '=this.ID' => 'ref.IBLOCK_ELEMENT_ID'
            ], [
               'join_type' => 'INNER'
            ]),
         ],
         'select' => ['ID']
      ])->fetch();

      return $elementDB;
   }

   // Преобразует домен
   protected function deleteDomain(string $url)
   {

      $url = str_replace(array("https://", "http://", "www.", "getbeer.ru"), "", $url);

      return $url;
   }

   // Добавления массива в базу
   public static function addElements(array $elemets, string $sectionID)
   {

      \Bitrix\Main\Loader::includeModule("iblock");
      \Bitrix\Main\Loader::includeModule("catalog");

      $elObj = new CIBlockElement;
      // Проверяем наличие элемента, если есть, обновляем, нет, добавляем
      foreach ($elemets as $item) {

         if (!$item) break;

         // Формируем массив
         $arLoadProductArray = $item;

         unset($arLoadProductArray['PRICE']);
         unset($arLoadProductArray['MEASURE']);
         unset($arLoadProductArray['MEASURE_DEFAULT']);

         if ($arLoadProductArray['DETAIL_PICTURE'] && $arLoadProductArray['PREVIEW_PICTURE']) {
            $arLoadProductArray['DETAIL_PICTURE'] = $arLoadProductArray['PREVIEW_PICTURE'] = CFile::MakeFileArray(self::deleteDomain($arLoadProductArray['DETAIL_PICTURE']));
            $arLoadProductArray['DETAIL_PICTURE']['del'] = $arLoadProductArray['PREVIEW_PICTURE']['del'] = 'Y';
         } else {
            $arLoadProductArray['DETAIL_PICTURE'] = $arLoadProductArray['PREVIEW_PICTURE'] = ['del' => 'Y'];
         }

         $elementID = self::checkElementField($item['PROPERTY_VALUES']['CML2_ARTICLE'], $sectionID)['ID'];



         if ($elementID) {
            // Обновляем элемент
            $elObj->Update($elementID, $arLoadProductArray);
         } else {
            // Добавляем элемент элемент
            $elementID = $elObj->Add($arLoadProductArray);
         }

         var_dump($elementID);

         // добавляем доп картинки
         if (!empty(array_diff($arLoadProductArray['PROPERTY_VALUES']['PHOTOS'], array('')))) {
            foreach ($arLoadProductArray['PROPERTY_VALUES']['PHOTOS'] as $key => $img) {
               $arLoadProductArray['PROPERTY_VALUES']['PHOTOS'][$key] = CFile::MakeFileArray(self::deleteDomain($img));
            }
         } else {
            $arLoadProductArray['PROPERTY_VALUES']['PHOTOS'] = ['del' => 'Y'];
         }
         CIBlockElement::SetPropertyValuesEx($elementID, self::$IBLOCK_ID, ['PHOTOS' => $arLoadProductArray['PROPERTY_VALUES']['PHOTOS']]);
         // }

         // Проверяем является торговым каталогом или нет, задает тип товара и единицу измерения
         $existProduct = \Bitrix\Catalog\Model\Product::getCacheItem($elementID, true);

         if (!empty($existProduct)) {
            \Bitrix\Catalog\Model\Product::update($elementID, ['MEASURE' => $item['MEASURE'], 'TYPE' => 1]);
         } else {
            \Bitrix\Catalog\Model\Product::add(['ID' => $elementID, 'MEASURE' => $item['MEASURE'], 'TYPE' => \Bitrix\Catalog\ProductTable::TYPE_PRODUCT]);
         }

         // Обновляем цену/кэф параметр

         CPrice::SetBasePrice($elementID, $item['PRICE'], \Bitrix\Currency\CurrencyManager::getBaseCurrency());

         $curElementRatio = CCatalogMeasureRatio::getList(
            [],
            [
               'IBLOCK_ID' => self::$IBLOCK_ID,
               'PRODUCT_ID' => $elementID
            ],
            false,
            false
         )->GetNext();


         if ($curElementRatio['ID']) {
            CCatalogMeasureRatio::update($curElementRatio['ID'], ['RATIO' => $item['MEASURE_DEFAULT']]);
         } else {
            CCatalogMeasureRatio::add(array('PRODUCT_ID' => $elementID, 'RATIO' => $item['MEASURE_DEFAULT']));
         }
      }

      return true;
   }
}
