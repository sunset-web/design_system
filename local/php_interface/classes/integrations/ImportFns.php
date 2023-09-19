<?
// Класс реализует импорт данных с ФНС по апи
class ImportFns
{

   // static private $secret_token = 'secret_token';
   static private $secret_token = 'secret_token';
   static protected $domain = 'https://api-fns.ru/api/';
   static protected $IblockId = 1;

   // Инициализация библиотек
   static protected function init()
   {

      \Bitrix\Main\Loader::includeModule("iblock");
   }
   // Создание объекта http
   static protected function createHttp()
   {
      return new \Bitrix\Main\Web\HttpClient();
   }
   // Получение статистики запросов
   static protected function getStats()
   {
      $httpClient = self::createHttp();

      $result_request = $httpClient->get(self::$domain . 'stat?key=' . self::$secret_token);
      $result = json_decode($result_request);

      return $result;
   }
   // Проверка статистики запросов
   static protected function checkStats(object $stats, string $method)
   {

      $error = true;

      try {

         if (isset($stats?->Методы?->$method)) {

            if ($stats->Методы->$method->Лимит == $stats->Методы->$method->Истрачено) {

               throw new \Bitrix\Main\SystemException("Превышены лимиты");
            }
         } else {

            throw new \Bitrix\Main\SystemException("Поле не существует");
         }
      } catch (\Bitrix\Main\SystemException $e) {
         $error = $e->getMessage();
      }

      return $error;
   }
   // Получаем информацию по компании
   static protected function getInfo(string $INN)
   {

      $httpClient = self::createHttp();

      $result_request = $httpClient->get(self::$domain . 'egr?req=' . $INN . '&key=' . self::$secret_token);
      $result = json_decode($result_request);

      return $result;
   }
   // Фильтрация компаний по КПП
   static protected function filterCompanies(string $INN, string $KPP)
   {

      $infoArray = self::getInfo($INN);

      $result = [];

      foreach ($infoArray->items as $item) {

         if ($item->ЮЛ->КПП == $KPP) {
            $result['INN'] = $item->ЮЛ->ИНН;
            $result['KPP'] = $item->ЮЛ->КПП;
            $result['OGRN'] = $item->ЮЛ->ОГРН;
            $result['FULL_NAME'] = $item->ЮЛ->НаимПолнЮЛ;
            $result['NAME'] = $item->ЮЛ->НаимСокрЮЛ;
            $result['LEGAL_ADDRESS'] = $item->ЮЛ->Адрес->АдресПолн;
            $result['PHYSICAL_ADDRESS'] = $item->ЮЛ->Адрес->АдресПолн;
            $result['RESPOSIBLE_NAME'] = $item->ЮЛ->Руководитель->ФИОПолн;
            $result['POSITION'] = $item->ЮЛ->Руководитель->Должн;
         }
      }

      return $result;
   }

   // Получение данных
   static public function getFNS(string $INN, string $KPP)
   {
      self::init();


      if ($INN && $KPP) {


         $stats = self::getStats($INN, $KPP);

         $checkArray = self::checkStats($stats, 'search');

         if ($checkArray === true) {

            $infoArray = self::filterCompanies($INN, $KPP);
            return $infoArray;
         } else {
            return $checkArray;
         }
      }

      return false;
   }
}
