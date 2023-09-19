<?

/**
 * TODO:
 * Методы для документации
 * checkToken - проверка токена, принимает токен, возвращает либо тру, либо ошибку
 * getCompanies - получение списка компаний, есть фильтрация, возвращает список, либо ошибку
 * getLicenses - получение списка лицензий, есть фильтрация, возвращает список, либо ошибку
 * getNews - получение списка новостей, есть фильтрация, возвращает список, либо ошибку
 * updateCompanies - обновляет список общих компаний и удаляет новые
 * addLicense - добавление лицензии, возвращает истину, либо ошибку
 * updateLicense - обновляет список общих лицензий и удаляет новые
 * addNews - добавление новости, возвращает истину, либо ошибку
 * updateNews - обновляет список общих новостей и удаляет новые
 * 204-нет содержимого
 * 401-ошибка авторизации
 * 403-нет доступа
 * 523-источник недоступен
 * 501-не реализовано
 * 520-не известная ошибка
 */
// Класс реализует АПИ сервиса для интеграции с вендорами
class ItinApi
{

   static protected $pathRoot = '';

   // Инициализация библиотек
   static protected function init()
   {
      \Bitrix\Main\Loader::includeModule("iblock");
      \Bitrix\Main\Loader::includeModule("highloadblock");

      self::$pathRoot = \Bitrix\Main\Application::getDocumentRoot();
   }
   // Формирование ошибки
   static private function getError($exception)
   {
      return \Bitrix\Main\Web\Json::encode(
         [
            'error' =>
            [
               'code' => $exception->getCode(),
               'text' => $exception->getMessage()
            ]
         ]
      );
   }
   // Получение токена из запроса
   static private function getToken($token)
   {
      $vendor = \Bitrix\Iblock\Elements\ElementApivendorTable::getList([
         'select' => ['ID', 'CODE'],
         'filter' => ['=ACTIVE' => 'Y', '=CODE' => $token],
      ])->fetch();

      return $vendor;
   }
   // Проверка токена токена
   static public function checkToken($token)
   {
      self::init();

      $tokenRes = self::getToken($token);
      // Если существует такой вендор возвращаем истину, иначе формируем ошибку авторизации
      try {
         if ($tokenRes) {
            return \Bitrix\Main\Web\Json::encode(['result' => true]);
         }

         throw new Bitrix\Main\SystemException("Authentication error", 401);
      } catch (Bitrix\Main\SystemException $exception) {
         return self::getError($exception);
      }
   }
   // Проверка файла и получение из него данных
   static private function getJson($path)
   {
      if (file_exists($path)) {
         // Получаем список компаний из файла
         return file_get_contents($path) ? \Bitrix\Main\Web\Json::decode(file_get_contents($path)) : [];
      }

      throw new Bitrix\Main\SystemException("Folder is unreachable", 523);

      return false;
   }
   // Получение списка компаний (всех/новых)
   static public function getCompanies($token, $filter = array())
   {
      self::init();
      // Проверка токена
      $tokenRes = self::checkToken($token);
      $tokenResDecode = \Bitrix\Main\Web\Json::decode(self::checkToken($token));
      // Выдаем ошибку, если неверный
      if (!$tokenResDecode['result']) return $tokenRes;
      // Если существует дерриктория получаем данные, иначе формируем ошибку
      try {
         if (file_exists(self::$pathRoot . '/upload/api/' . $token . '/')) {
            // Если есть фильтр новых компаний
            if ($filter['new'] != 'Y') {
               $companyRes = self::getJson(self::$pathRoot . '/upload/api/' . $token . '/company.json');
               $companyRes = array_slice($companyRes, $filter['offset'], $filter['limit']);

               return \Bitrix\Main\Web\Json::encode(['result' => $companyRes]);
            } else {
               $companyRes = self::getJson(self::$pathRoot . '/upload/api/' . $token . '/company_new.json');
               $companyRes = array_slice($companyRes, $filter['offset'], $filter['limit']);

               // Меняем статус компаний
               foreach ($companyRes as $key => $company) {
                  $companyRes[$key]['CHECKED'] = 'Y';
               }
               // записываем в файл
               file_put_contents(self::$pathRoot . '/upload/api/' . $token . '/company_new.json', \Bitrix\Main\Web\Json::encode($companyRes));

               return \Bitrix\Main\Web\Json::encode(['result' => $companyRes]);
            }
         }

         throw new Bitrix\Main\SystemException("Folder is unreachable", 523);
      } catch (Bitrix\Main\SystemException $exception) {
         return self::getError($exception);
      }
   }

   // Получение списка лицензий (всех/новых)
   static public function getLicenses($token, $filter = array())
   {
      self::init();
      // Проверка токена
      $tokenRes = self::checkToken($token);
      $tokenResDecode = \Bitrix\Main\Web\Json::decode(self::checkToken($token));
      // Выдаем ошибку, если неверный
      if (!$tokenResDecode['result']) return $tokenRes;
      // Если существует дерриктория получаем данные, иначе формируем ошибку
      try {
         if (file_exists(self::$pathRoot . '/upload/api/' . $token . '/')) {
            // Если есть фильтр новых лицензий
            if ($filter['new'] != 'Y') {
               $licenseRes = self::getJson(self::$pathRoot . '/upload/api/' . $token . '/license.json');
            } else {
               $licenseRes = self::getJson(self::$pathRoot . '/upload/api/' . $token . '/license_new.json');
            }
            $licenseRes = array_slice($licenseRes, $filter['offset'], $filter['limit']);

            return \Bitrix\Main\Web\Json::encode(['result' => $licenseRes]);
         }

         throw new Bitrix\Main\SystemException("Folder is unreachable", 523);
      } catch (Bitrix\Main\SystemException $exception) {
         return self::getError($exception);
      }
   }
   // Получение списка новостей (всех/новых)
   static public function getNews($token, $filter = array())
   {
      self::init();
      // Проверка токена
      $tokenRes = self::checkToken($token);
      $tokenResDecode = \Bitrix\Main\Web\Json::decode(self::checkToken($token));
      // Выдаем ошибку, если неверный
      if (!$tokenResDecode['result']) return $tokenRes;
      // Если существует дерриктория получаем данные, иначе формируем ошибку
      try {
         if (file_exists(self::$pathRoot . '/upload/api/' . $token . '/')) {
            // Если есть фильтр новых лицензий
            if ($filter['new'] != 'Y') {
               $newsRes = self::getJson(self::$pathRoot . '/upload/api/' . $token . '/news.json');
            } else {
               $newsRes = self::getJson(self::$pathRoot . '/upload/api/' . $token . '/news_new.json');
            }
            $newsRes = array_slice($newsRes, $filter['offset'], $filter['limit']);

            return \Bitrix\Main\Web\Json::encode(['result' => $newsRes]);
         }

         throw new Bitrix\Main\SystemException("Folder is unreachable", 523);
      } catch (Bitrix\Main\SystemException $exception) {
         return self::getError($exception);
      }
   }
   // Обновление списка компаний
   static public function updateCompanies($token, $arrayIDs)
   {
      self::init();
      // Проверка токена
      $tokenRes = self::checkToken($token);
      $tokenResDecode = \Bitrix\Main\Web\Json::decode(self::checkToken($token));
      // Выдаем ошибку, если неверный
      if (!$tokenResDecode['result']) return $tokenRes;
      // Если существует дерриктория получаем данные, иначе формируем ошибку
      try {
         // Выдаем ошибку, если нет ID для записи
         if (empty($arrayIDs)) throw new Bitrix\Main\SystemException("No content", 204);
         // Если существует раздел
         if (file_exists(self::$pathRoot . '/upload/api/' . $token . '/')) {
            $companyNewRes = self::getJson(self::$pathRoot . '/upload/api/' . $token . '/company_new.json');
            $companyRes = self::getJson(self::$pathRoot . '/upload/api/' . $token . '/company.json');
            // добавляем в общие компании и очищаем из новых
            foreach ($companyNewRes as $key => $company) {
               if (in_array($company['ID'], $arrayIDs) && $company['CHECKED'] == 'Y') {
                  array_push($companyRes, $company);
                  unset($companyNewRes[$key]);
               }
            }
            sort($companyNewRes);

            file_put_contents(self::$pathRoot . '/upload/api/' . $token . '/company_new.json', \Bitrix\Main\Web\Json::encode($companyNewRes));
            file_put_contents(self::$pathRoot . '/upload/api/' . $token . '/company.json', \Bitrix\Main\Web\Json::encode($companyRes));


            return \Bitrix\Main\Web\Json::encode(['result' => true]);
         }

         throw new Bitrix\Main\SystemException("Folder is unreachable", 523);
      } catch (Bitrix\Main\SystemException $exception) {
         return self::getError($exception);
      }
   }
   // Обновление списка лицензий
   static public function updateLicenses($items = array())
   {
      self::init();

      $files_new = glob(self::$pathRoot . '/upload/api/*/license_new.json');
      // записываем в файл
      foreach ($files_new as $file) {
         $licenseNewRes = self::getJson($file);
         $licenseRes = self::getJson(str_replace('_new', '', $file));
         // добавляем в общие компании и очищаем из новых
         foreach ($licenseNewRes as $key => $license) {
            if ($license['CHECKED'] == 'N' && !in_array($license['ARTICLE'], $items)) {
               // Получаем компанию по ИНН + КПП
               $company = \Bitrix\Iblock\Elements\ElementCompaniesTable::getList([
                  'select' => ['ID', 'INN_' => 'INN', 'KPP_' => 'KPP'],
                  'filter' => ['=INN_VALUE' => $license['INN'], '=KPP_VALUE' => $license['KPP']],
               ])->fetch();
               // Добавление в иб
               $el = new CIBlockElement;
               $PROP = [
                  'ARTICLE' => $license['ARTICLE'],
                  'START_DATE' => $license['START_DATE'],
                  'END_DATE' => $license['END_DATE'],
                  'PRICE' => $license['PRICE'],
                  'COMPANY' => $company['ID'],
                  'API' => ['VALUE' => 12],
               ];
               $arLoadProductArray = array(
                  "IBLOCK_ID"      => 5,
                  "PROPERTY_VALUES" => $PROP,
                  "NAME"           => $license['NAME'],
                  "ACTIVE"         => "Y",
               );
               $el->Add($arLoadProductArray);
               $license['CHECKED'] = 'Y';
               array_push($licenseRes, $license);
               array_push($items, $license['ARTICLE']);
            }
         }
         $licenseNewResBefore = self::getJson($file);
         if ($licenseNewResBefore == $licenseNewRes) {
            foreach ($licenseNewRes as $key => $license) {
               if (in_array($license['ARTICLE'], $items)) {
                  unset($licenseNewRes[$key]);
               }
            }
            sort($licenseNewRes);
            file_put_contents($file, \Bitrix\Main\Web\Json::encode($licenseNewRes));
            file_put_contents(str_replace('_new', '', $file), \Bitrix\Main\Web\Json::encode($licenseRes));

            return 'ItinApi::updateLicenses();';
         } else {
            self::updateLicenses($items);
         }
      }
   }
   // Добавление новых лицензий
   static public function addLicenses($token, $items)
   {
      self::init();
      // Проверка токена
      $tokenRes = self::checkToken($token);
      $tokenResDecode = \Bitrix\Main\Web\Json::decode(self::checkToken($token));
      // Выдаем ошибку, если неверный
      if (!$tokenResDecode['result']) return $tokenRes;
      // Если существует дерриктория получаем данные, иначе формируем ошибку
      try {
         // Выдаем ошибку, если нет записей
         if (empty($items)) throw new Bitrix\Main\SystemException("No content", 204);
         // Если существует раздел
         if (file_exists(self::$pathRoot . '/upload/api/' . $token . '/')) {
            $licenseRes = self::getJson(self::$pathRoot . '/upload/api/' . $token . '/license_new.json');
            // Добавляем флаг
            foreach ($items as $key => $item) {
               if (!empty($licenseRes)) {
                  if (array_search($item['ARTICLE'], array_column($licenseRes, 'ARTICLE')) === false) {
                     $item['CHECKED'] = 'N';
                     array_push($licenseRes, $item);
                  }
               } else {
                  $item['CHECKED'] = 'N';
                  array_push($licenseRes, $item);
               }
            }
            // записываем в файл
            file_put_contents(self::$pathRoot . '/upload/api/' . $token . '/license_new.json', \Bitrix\Main\Web\Json::encode($licenseRes));

            return \Bitrix\Main\Web\Json::encode(['result' => true]);
         }

         throw new Bitrix\Main\SystemException("Folder is unreachable", 523);
      } catch (Bitrix\Main\SystemException $exception) {
         return self::getError($exception);
      }
   }
   // Обновление списка новостей
   static public function updateNews($items = array())
   {
      self::init();

      $files_new = glob(self::$pathRoot . '/upload/api/*/news_new.json');

      $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById(11)->fetch();
      $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
      $entity_data_class = $entity->getDataClass();
      // записываем в файл
      foreach ($files_new as $file) {
         $newsNewRes = self::getJson($file);
         $newsRes = self::getJson(str_replace('_new', '', $file));
         preg_match('|/upload/api/(.*?)/news_new.json|si', $file, $match);
         $token = $match[1];
         $vendor = self::getToken($token);
         // добавляем в общие компании и очищаем из новых
         foreach ($newsNewRes as $key => $news) {
            if ($news['CHECKED'] == 'N' && !in_array($news['ID'], $items)) {
               // Получаем компанию по ИНН + КПП
               $company = \Bitrix\Iblock\Elements\ElementCompaniesTable::getList([
                  'select' => ['ID', 'INN_' => 'INN', 'KPP_' => 'KPP'],
                  'filter' => ['=INN_VALUE' => $news['INN'], '=KPP_VALUE' => $news['KPP']],
               ])->fetch();
               // Добавление в хб
               $entity_data_class::add(array(
                  'UF_TITLE' => $news['TITLE'],
                  'UF_VIEW' => 'false',
                  'UF_COMPANY' => [$company['ID']],
                  'UF_VENDOR' => $vendor['ID'],
                  'UF_DATE' => $news['DATE'],
                  'UF_TEXT' => $news['TEXT'],
               ))->getPrimary();

               $news['CHECKED'] = 'Y';
               array_push($newsRes, $news);
               array_push($items, $news['ID']);
            }
         }
         $licenseNewResBefore = self::getJson($file);
         if ($licenseNewResBefore == $newsNewRes) {
            foreach ($newsNewRes as $key => $news) {
               if (in_array($news['ID'], $items)) {
                  unset($newsNewRes[$key]);
               }
            }
            sort($newsNewRes);
            file_put_contents($file, \Bitrix\Main\Web\Json::encode($newsNewRes));
            file_put_contents(str_replace('_new', '', $file), \Bitrix\Main\Web\Json::encode($newsRes));

            // return 'CustomApi::updateNews();';
         } else {
            self::updateNews($items);
         }
      }
   }
   // Добавление новых новстей
   static public function addNews($token, $items)
   {
      self::init();
      // Проверка токена
      $tokenRes = self::checkToken($token);
      $tokenResDecode = \Bitrix\Main\Web\Json::decode(self::checkToken($token));
      // Выдаем ошибку, если неверный
      if (!$tokenResDecode['result']) return $tokenRes;
      // Если существует дерриктория получаем данные, иначе формируем ошибку
      try {
         // Выдаем ошибку, если нет записей
         if (empty($items)) throw new Bitrix\Main\SystemException("No content", 204);
         // Если существует раздел
         if (file_exists(self::$pathRoot . '/upload/api/' . $token . '/')) {
            $newsRes = self::getJson(self::$pathRoot . '/upload/api/' . $token . '/news_new.json');
            // Добавляем флаг
            foreach ($items as $key => $item) {
               if (!empty($newsRes)) {
                  if (array_search($item['ARTICLE'], array_column($newsRes, 'ARTICLE')) === false) {
                     $item['CHECKED'] = 'N';
                     array_push($newsRes, $item);
                  }
               } else {
                  $item['CHECKED'] = 'N';
                  array_push($newsRes, $item);
               }
            }
            // записываем в файл
            file_put_contents(self::$pathRoot . '/upload/api/' . $token . '/news_new.json', \Bitrix\Main\Web\Json::encode($newsRes));

            return \Bitrix\Main\Web\Json::encode(['result' => true]);
         }

         throw new Bitrix\Main\SystemException("Folder is unreachable", 523);
      } catch (Bitrix\Main\SystemException $exception) {
         return self::getError($exception);
      }
   }
}
