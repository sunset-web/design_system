<?
// Добавление вендора
function vendoreCreate(&$result)
{
   if ($result['IBLOCK_ID'] == 11) {

      $result['CODE'] = randString(18);

      $rootDoc = \Bitrix\Main\Application::getDocumentRoot();

      mkdir($rootDoc . "/upload/api/" . $result['CODE'], 0750);

      file_put_contents($rootDoc . "/upload/api/" . $result['CODE'] . '/company_new.json', '');
      file_put_contents($rootDoc . "/upload/api/" . $result['CODE'] . '/company.json', '');
      file_put_contents($rootDoc . "/upload/api/" . $result['CODE'] . '/license.json', '');
      file_put_contents($rootDoc . "/upload/api/" . $result['CODE'] . '/license_new.json', '');
      file_put_contents($rootDoc . "/upload/api/" . $result['CODE'] . '/news.json', '');
      file_put_contents($rootDoc . "/upload/api/" . $result['CODE'] . '/news_new.json', '');
   }
}
// Удаление вендора
function vendoreDelete($result)
{
   if ($result['IBLOCK_ID'] == 11) {

      $rootDoc = \Bitrix\Main\Application::getDocumentRoot();

      unlink($rootDoc . "/upload/api/" . $result['CODE'] . '/company_new.json');
      unlink($rootDoc . "/upload/api/" . $result['CODE'] . '/company.json');
      unlink($rootDoc . "/upload/api/" . $result['CODE'] . '/license.json');
      unlink($rootDoc . "/upload/api/" . $result['CODE'] . '/license_new.json');
      unlink($rootDoc . "/upload/api/" . $result['CODE'] . '/news.json');
      unlink($rootDoc . "/upload/api/" . $result['CODE'] . '/news_new.json');

      rmdir($rootDoc . "/upload/api/" . $result['CODE']);
   }
}
// Добавление компании
function companyAdd(&$result)
{
   if ($result['IBLOCK_ID'] == 1) {

      $rootDoc = \Bitrix\Main\Application::getDocumentRoot();
      $addInfo = [
         'ID' => $result['ID'],
         'NAME' => $result['NAME'],
         'INN' => $result['PROPERTY_VALUES']['1']['n0']['VALUE'],
         'KPP' => $result['PROPERTY_VALUES']['2']['n0']['VALUE'],
         'CHECKED' => 'N',
      ];
      $files = glob($rootDoc . '/upload/api/*/company_new.json');
      // записываем в файл
      foreach ($files as $file) {
         $array = file_get_contents($file) ? \Bitrix\Main\Web\Json::decode(file_get_contents($file)) : array();
         array_push($array, $addInfo);
         file_put_contents($file, \Bitrix\Main\Web\Json::encode($array));
      }
   }
}
// Удаление компании
function companyDelete(&$result)
{
   if ($result['IBLOCK_ID'] == 1) {

      $rootDoc = \Bitrix\Main\Application::getDocumentRoot();

      $files = glob($rootDoc . '/upload/api/*/company.json');
      foreach ($files as $file) {
         $array = file_get_contents($file) ? \Bitrix\Main\Web\Json::decode(file_get_contents($file)) : array();
         // Удаляем элемент массива
         unset($array[array_search($result['ID'], array_column($array, 'ID'))]);
         sort($array);
         file_put_contents($file, \Bitrix\Main\Web\Json::encode($array));
      }
      $files = glob($rootDoc . '/upload/api/*/company_new.json');
      foreach ($files as $file) {
         $array = file_get_contents($file) ? \Bitrix\Main\Web\Json::decode(file_get_contents($file)) : array();
         // Удаляем элемент массива
         unset($array[array_search($result['ID'], array_column($array, 'ID'))]);
         sort($array);
         file_put_contents($file, \Bitrix\Main\Web\Json::encode($array));
      }
   }
}
