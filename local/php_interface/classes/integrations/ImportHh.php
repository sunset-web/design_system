<?
// Класс реализует импорт данных с HeadHunter по апи
class ImportHh
{

   static private $client_id = 'client_id';
   static private $client_secret = 'client_secret';
   static private $auth_token = 'auth_token';
   static private $access_token_user = 'access_token_user';
   // static private $refresh_token_user = 'refresh_token_user';

   static protected $domain = 'https://hh.ru/';
   static protected $domainApi = 'https://api.hh.ru/';
   static protected $domainRedirect = 'domainRedirect';
   static protected $IblockId = 13;
   static protected $IblockIdCity = 2;
   static protected $userAgent = 'Список вакансий/1.0 (ramos7691@gmail.com)';

   // Инициализация библиотек
   static protected function init()
   {

      \Bitrix\Main\Loader::includeModule("iblock");
   }
   // Получение токена пользователя 
   // (1. Получаем auth_token пользователя переходим по ссылке https://hh.ru/oauth/authorize?response_type=code&client_id={client_id})
   // (2. Получаем пару access_token_user и refresh_token_user через функцию getToken)
   // Проверка токена  ПЕРЕДЕЛАТь!
   // static protected function checkToken()
   // {   

   //     $httpClient = new \Bitrix\Main\Web\HttpClient();

   //     $result_request = $httpClient->post(self::$domain . 'oauth/token', ['grant_type' => 'refresh_token', 'refresh_token' => self::$refresh_token_user]);
   //     $result = json_decode($result_request);

   //     return $result ? $result : false;


   //     $httpClient = self::createHttp();

   //     $result_request = $httpClient->get(self::$domainApi . 'me');
   //     $result = json_decode($result_request);

   //     if ($result->oauth_error) {

   //         self::$access_token_user = self::getToken() || self::$access_token_user;

   //         return self::checkToken();
   //     } else {

   //         return $result;
   //     }
   // }
   static protected function getToken()
   {
      $httpClient = new \Bitrix\Main\Web\HttpClient();

      $result_request = $httpClient->post(self::$domain . 'oauth/token', ['grant_type' => 'authorization_code', 'client_id' => self::$client_id, 'client_secret' => self::$client_secret, 'code' => self::$auth_token, 'redirect_uri' => self::$domainRedirect]);
      $result = json_decode($result_request);

      return $result ? $result : false;
   }
   // Создание объекта с заголовком
   static private function createHttp()
   {
      return new \Bitrix\Main\Web\HttpClient(
         [
            'headers' =>
            [
               'User-Agent' => self::$userAgent,
               'Authorization' => 'Bearer ' . self::$access_token_user
            ]
         ]
      );
   }
   // Проверка id работодателя
   static protected function getEmployerId()
   {
      $httpClient = self::createHttp();

      $result_request = $httpClient->get(self::$domainApi . 'me');
      $result = json_decode($result_request);

      return $result ? $result->employer->id : false;
   }
   // Список менеджеров
   static protected function getManagers(string $employerId)
   {
      $httpClient = self::createHttp();

      $result_request = $httpClient->get(self::$domainApi . 'employers/' . $employerId . '/managers');
      $result = json_decode($result_request);

      foreach ($result->items as $item) {
         if ($item->vacancies_count) {
            $resultArray[] = $item->id;
         }
      }

      return $resultArray;
   }

   // Список вакансий менеджера
   static protected function getVacancies(array $managersList, string $employerId)
   {
      $httpClient = self::createHttp();
      $resultArray = [];

      foreach ($managersList as $item) {
         $result_request = $httpClient->get(self::$domainApi . 'employers/' . $employerId . '/vacancies/active?manager_id=' . $item);
         $resultArray = array_merge($resultArray, json_decode($result_request)->items);
      }

      return $resultArray;
   }
   // Список вакансий с сайта
   static protected function getVacancySite()
   {
      $resultQuery = \Bitrix\Iblock\ElementTable::getList(array(
         'select' => array('ID', 'NAME', 'OLD_ID' => 'PROP.VALUE'),
         'filter' => array('IBLOCK_ID' => self::$IblockId, 'PROP.IBLOCK_PROPERTY_ID' => 684),
         'runtime' => [
            new \Bitrix\Main\Entity\ReferenceField('PROP', \Bitrix\Iblock\ElementPropertyTable::getEntity(), [
               '=this.ID' => 'ref.IBLOCK_ELEMENT_ID'
            ], [
               'join_type' => 'INNER'
            ]),
         ],

      ))->FetchAll();

      foreach ($resultQuery as $item) {
         $result[$item['OLD_ID']] = $item['ID'];
      }

      return $result;
   }
   // Получение списка городов
   static protected function getCities()
   {
      $resultQuery = \Bitrix\Iblock\ElementTable::getList(array(
         'select' => array('ID', 'NAME'),
         'filter' => array('IBLOCK_ID' => self::$IblockIdCity),

      ))->FetchAll();

      foreach ($resultQuery as $item) {
         $result[$item['NAME']] = $item['ID'];
      }

      return $result;
   }
   // Создание вакансий на сайте
   static protected function addVacancies(array $vacansiesList)
   {

      $citiesList = self::getCities();
      $vacansiesListSite = self::getVacancySite();

      foreach ($vacansiesList as $item) {
         if (array_key_exists($item->address->city, $citiesList)) {

            $element = new CIBlockElement;

            $PROP = array();
            $PROP['LINK'] = $item->alternate_url;
            $PROP['OLD_ID'] = $item->id;
            $PROP['LINK_REGION'] = $citiesList[$item->address->city];
            $PROP['PAY'] = $item->salary->from;
            $PROP['CITY'] = $item->address->city;

            $arLoadProductArray = array(
               "IBLOCK_SECTION_ID" => 229,
               "IBLOCK_ID" => self::$IblockId,
               "PROPERTY_VALUES" => $PROP,
               "NAME"           => $item->name,
               "CODE"           => Cutil::translit($item->name, "ru") . '_' . $item->id,
               "ACTIVE"         => "Y",
            );

            if (array_key_exists($item->id, $vacansiesListSite)) {
               $element->Update($vacansiesListSite[$item->id], $arLoadProductArray);
               unset($vacansiesListSite[$item->id]);
            } else {
               $element->Add($arLoadProductArray);
            }
         }
      }
      // отключенные вакансии на сайте
      foreach ($vacansiesListSite as $item) {

         $element = new CIBlockElement;

         $element->Update($item, ["ACTIVE" => "N"]);
      }
   }
   // Получение данных
   static public function getHH()
   {
      self::init();

      $employerId = self::getEmployerId();
      $managersList = self::getManagers($employerId);
      $vacansiesList = self::getVacancies($managersList, $employerId);
      self::addVacancies($vacansiesList);
   }

   // Получение данных через агента
   static public function getHHAgent()
   {
      self::getHH();

      return 'ImportHh::getHHAgent();';
   }
}
