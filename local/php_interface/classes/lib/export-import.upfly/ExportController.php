<?
// Класс реализует создание файла на сервере и его выгрузку
class ExportController extends \IRestService
{
   // Стандартная функция формирования rest
   public static function OnRestServiceBuildDescription()
   {
      return array(
         'exportController' => array(
            'exportController.download' => array(
               'callback' => array(__CLASS__, 'download'),
               'options' => array(),
            ),
            'exportController.deleteFile' => array(
               'callback' => array(__CLASS__, 'deleteFile'),
               'options' => array(),
            ),
         )
      );
   }

   // Инициализируем необходимые данные
   protected static function init()
   {
      if (!\Bitrix\Main\Loader::includeModule('nkhost.phpexcel')) {
         throw new \Bitrix\Rest\RestException('Ошибка подключения модуля nkhost.phpexcel', "LOADER_ERROR");
      }
      if (!\Bitrix\Main\Loader::includeModule('iblock')) {
         throw new \Bitrix\Rest\RestException('Ошибка подключения модуля iblock', "LOADER_ERROR");
      }
      if (!\Bitrix\Main\Loader::includeModule('catalog')) {
         throw new \Bitrix\Rest\RestException('Ошибка подключения модуля catalog', "LOADER_ERROR");
      }

      define('LIMIT_STEP', 50);
      define('PATH_FILE', '/home/bitrix/www/upload/export.upfly/template.xlsx');

      global $PHPEXCELPATH;

      require_once($PHPEXCELPATH . '/PHPExcel/IOFactory.php');
      require_once($PHPEXCELPATH . '/PHPExcel.php');
      require_once($PHPEXCELPATH . '/PHPExcel/Writer/Excel5.php');
   }

   // Имя сервера
   protected static function getServerName()
   {
      $context = \Bitrix\Main\Application::getInstance()->getContext();
      $server = $context->getServer();
      $https = $server->get('HTTPS') == 'on' ? 'https' : 'http';
      $serverName = $https . '://' . $server->getServerName();
      return $serverName;
   }
   // Изменяем урл
   protected static function editDomainStr($str)
   {
      $serverName = self::getServerName();
      $domain = str_replace($serverName, '', $str);

      return $domain;
   }

   // Получаем список товаров
   protected static function GetList($sectionID, $step = 0, &$resultArray)
   {

      // список товаров с ценой, полями и форматом продажи
      $resultQuery = \Bitrix\Iblock\ElementTable::getList([
         'filter'  => ["IBLOCK_SECTION_ID" => $sectionID, "IBLOCK_ID" => 2],
         'limit'   => LIMIT_STEP,
         'offset'  => $step,
         'runtime' => array(
            new Bitrix\Main\ORM\Fields\Relations\Reference(
               'PROP_EL',
               '\Bitrix\Iblock\ElementPropertyTable',
               [
                  '=ref.IBLOCK_ELEMENT_ID' => 'this.ID',
               ]
            ),
            new Bitrix\Main\ORM\Fields\Relations\Reference(
               'PROP',
               '\Bitrix\Iblock\PropertyTable',
               [
                  '=ref.ID' => 'this.PROP_EL.IBLOCK_PROPERTY_ID'
               ],
            ),
            new Bitrix\Main\ORM\Fields\Relations\Reference(
               'PRICE',
               '\Bitrix\Catalog\PriceTable',
               [
                  '=ref.PRODUCT_ID' => 'this.ID'
               ],
            ),
            new Bitrix\Main\ORM\Fields\Relations\Reference(
               'MEASURE',
               '\Bitrix\Catalog\MeasureRatioTable',
               [
                  '=ref.PRODUCT_ID' => 'this.ID'
               ],
            ),
            new Bitrix\Main\ORM\Fields\ExpressionField(
               'PROPERTY_CODE',
               "CONCAT('PROPERTY_', %s)",
               'PROP.CODE'
            ),
         ),
         'select'  =>  [
            'ID', 'NAME', 'DETAIL_TEXT', 'DETAIL_PICTURE', 'PROPERTY_CODE',
            "PROPERTY_VALUE" => 'PROP_EL.VALUE',
            "PROPERTY_TYPE" => 'PROP.PROPERTY_TYPE',
            "PRODUCT_PRICE" => 'PRICE.PRICE',
            "PRODUCT_RATIO" => 'MEASURE.RATIO',

         ],
      ])->FetchAll();


      $serverName = self::getServerName();

      // Преобразуем к нужному виду
      foreach ($resultQuery as $res) {

         $resultArray[$res['ID']]['NAME'] = $res['NAME'];
         $resultArray[$res['ID']]['DETAIL_TEXT'] = $res['DETAIL_TEXT'];
         $resultArray[$res['ID']]['DETAIL_PICTURE'] = \CFile::GetPath($res['DETAIL_PICTURE']) ? self::editDomainStr($serverName . \CFile::GetPath($res['DETAIL_PICTURE'])) : '';
         $resultArray[$res['ID']]['PRICE'] = $res['PRODUCT_PRICE'];
         $resultArray[$res['ID']]['MEASURE_RATIO'] = $res['PRODUCT_RATIO']; //******** */
         $resultArray[$res['ID']]['PROPS'][$res['PROPERTY_CODE']]['TYPE'] = $res['PROPERTY_TYPE'];

         switch ($res['PROPERTY_CODE']) {
            case "PROPERTY_STORAGE_CONDITIONS": //текстовое поле
            case "PROPERTY_COMPOUND": //текстовое поле

               $val = unserialize($res['PROPERTY_VALUE']);
               $resultArray[$res['ID']]['PROPS'][$res['PROPERTY_CODE']]['VALUE'][] = $val['TEXT'];
               break;

            case "PROPERTY_MEASURE": //список

               $resultEnum = \Bitrix\Iblock\PropertyEnumerationTable::getList([
                  'filter'  => ["ID" => $res['PROPERTY_VALUE']],
                  'select'  =>  ['XML_ID'],
                  'limit' => 1,
               ])->Fetch();

               $resultArray[$res['ID']]['PROPS'][$res['PROPERTY_CODE']]['VALUE'][] = str_replace('measure', '', $resultEnum['XML_ID']);

               break;

            case "PROPERTY_CATEGORIES": //список
            case "PROPERTY_TYPE": //список


               $resultEnum = \Bitrix\Iblock\PropertyEnumerationTable::getList([
                  'filter'  => ["ID" => $res['PROPERTY_VALUE']],
                  'select'  =>  ['VALUE'],
                  'limit' => 1,
               ])->Fetch();

               $resultArray[$res['ID']]['PROPS'][$res['PROPERTY_CODE']]['VALUE'][] = $resultEnum['VALUE'];

               break;

            case "PROPERTY_AGE": //чекбокс

               if ($res['PROPERTY_VALUE']) {
                  $resultArray[$res['ID']]['PROPS'][$res['PROPERTY_CODE']]['VALUE'][] = '1';
               } else {
                  $resultArray[$res['ID']]['PROPS'][$res['PROPERTY_CODE']]['VALUE'][] = '0';
               }
               break;

            case "PROPERTY_PHOTOS": //фотки

               $resultArray[$res['ID']]['PROPS'][$res['PROPERTY_CODE']]['VALUE'][] = \CFile::GetPath($res['PROPERTY_VALUE']) ? self::editDomainStr($serverName . \CFile::GetPath($res['PROPERTY_VALUE'])) : '';
               break;

            default: //строка
               $resultArray[$res['ID']]['PROPS'][$res['PROPERTY_CODE']]['VALUE'][] = $res['PROPERTY_VALUE'];
         }
      }

      return $resultQuery;
   }

   // Массив значений в строку
   protected static function strArrProp($arrProps)
   {
      $prop = '';
      foreach ($arrProps as $key => $value) {
         $prop .= ($key > 0 ? '|' : '') . $value;
      }
      return $prop;
   }

   // Запись в файл
   protected static function writeFile($items)
   {
      $file_clone_path = '/home/bitrix/www/upload/export.upfly/export.' . date("d.m.Y_h:i:s") . '.xls';

      copy(PATH_FILE, $file_clone_path);

      $xls = PHPExcel_IOFactory::load($file_clone_path);
      $xls->setActiveSheetIndex(1);
      $sheet = $xls->getActiveSheet();

      $i = 2;
      foreach ($items as $key => $item) {
         switch ($item['MEASURE_RATIO']) {
            case '1':
               $MEASURE_RATIO = '2';
               $PROPERTY_MEASURE = $item['PROPS']['PROPERTY_MEASURE']['VALUE'][0];
               $PROPERTY_VOLUME = $item['PROPS']['PROPERTY_VOLUME']['VALUE'][0];
               break;
            case '50':
               $MEASURE_RATIO = '0';
               $PROPERTY_MEASURE = '';
               $PROPERTY_VOLUME = '';
               break;
            case '0.5':
               $MEASURE_RATIO = '1';
               $PROPERTY_MEASURE = '';
               $PROPERTY_VOLUME = '';
               break;
         }
         for ($j = 0; $j < 28; $j++) {
            switch ($j) {
               case 0:
                  $prop = $item['NAME'];
                  break;
               case 1:
                  $prop = $item['PROPS']['PROPERTY_COMPOUND']['VALUE'][0];
                  break;
               case 2:
                  $prop = $item['DETAIL_TEXT'];
                  break;
               case 3:
                  $prop = $item['DETAIL_PICTURE'];
                  break;
               case 4:
                  $prop = $item['PROPS']['PROPERTY_CML2_ARTICLE']['VALUE'][0];
                  break;
               case 5:
                  $prop = $item['PROPS']['PROPERTY_AGE']['VALUE'][0] ? $item['PROPS']['PROPERTY_AGE']['VALUE'][0] : '0';
                  break;
               case 6:
                  $prop = $item['PRICE'];
                  break;
               case 7:
                  $prop = $item['PROPS']['PROPERTY_OLD_PRICE']['VALUE'][0];
                  break;
               case 8:
                  $prop = self::strArrProp($item['PROPS']['PROPERTY_CATEGORIES']['VALUE']);
                  break;
               case 9:
                  $prop = $MEASURE_RATIO;
                  break;
               case 10:
                  $prop = $PROPERTY_MEASURE;
                  break;
               case 11:
                  $prop = $PROPERTY_VOLUME;
                  break;
               case 12:
                  $prop = self::strArrProp($item['PROPS']['PROPERTY_PHOTOS']['VALUE']);
                  break;
               case 13:
                  $prop = $item['PROPS']['PROPERTY_STORAGE_CONDITIONS']['VALUE'][0];
                  break;
               case 14:
                  $prop = $item['PROPS']['PROPERTY_CALORIES']['VALUE'][0];
                  break;
               case 15:
                  $prop = $item['PROPS']['PROPERTY_PROTEINS']['VALUE'][0];
                  break;
               case 16:
                  $prop = $item['PROPS']['PROPERTY_FATS']['VALUE'][0];
                  break;
               case 17:
                  $prop = $item['PROPS']['PROPERTY_CARBOHYDRATES']['VALUE'][0];
                  break;
               case 18:
                  $prop = $item['PROPS']['PROPERTY_BRAND']['VALUE'][0];
                  break;
               case 19:
                  $prop = $item['PROPS']['PROPERTY_COUNTRY']['VALUE'][0];
                  break;
               case 20:
                  $prop = $item['PROPS']['PROPERTY_FORTRESS']['VALUE'][0];
                  break;
               case 21:
                  $prop = $item['PROPS']['PROPERTY_COLOR']['VALUE'][0];
                  break;
               case 22:
                  $prop = $item['PROPS']['PROPERTY_DENSITY']['VALUE'][0];
                  break;
               case 23:
                  $prop = self::strArrProp($item['PROPS']['PROPERTY_TYPE']['VALUE']);
                  break;
               case 24:
                  $prop = $item['PROPS']['PROPERTY_VIEW']['VALUE'][0];
                  break;
               case 25:
                  $prop = $item['PROPS']['PROPERTY_TASTE']['VALUE'][0];
                  break;
               case 26:
                  $prop = $item['PROPS']['PROPERTY_MANUFACTURER']['VALUE'][0];
                  break;
               default:
                  $prop = '';
                  break;
            }
            $sheet->setCellValueByColumnAndRow($j, $i, (string)$prop);
         }
         $i++;
      }
      unset($i);

      for ($i = 1; $i <= $sheet->getHighestRow(); $i++) {
         $nColumn = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());
         for ($j = 0; $j < $nColumn; $j++) {
            $arProducts[$i][$j] = $sheet->getCellByColumnAndRow($j, $i)->getValue();
         }
      }

      $objWriter = new PHPExcel_Writer_Excel5($xls);
      $objWriter->save($file_clone_path);

      $serverName = self::getServerName();

      $downloadPath = $serverName .  '/upload/export.upfly/' . basename($file_clone_path);
      return array($downloadPath, $file_clone_path);
   }

   // Выводим результат
   public static function download($query)
   {
      if ($query['error']) {
         throw new \Bitrix\Rest\RestException('Ошибка запроса', "QUERY_ERROR");
      }

      self::init();

      $resultItems = [];
      $step = 0;

      do {
         $items = self::GetList($query['sectionID'], $step, $resultItems);

         if (!$items) continue;

         $step += LIMIT_STEP;
      } while (!empty($items));

      list($link, $del_path) = self::writeFile($resultItems);
      return array('link' => $link, 'del_path' => $del_path, 'items' => $resultItems);
   }

   // Удаляем временный файл
   public static function deleteFile($path)
   {
      unlink($path['path']);
   }
}
