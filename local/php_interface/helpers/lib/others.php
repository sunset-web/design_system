<?

/**
 * Возвращает отформатированное значени телефона
 * @param $tel - телефон для форматирования
 * @return string 
 */
function formatPhone($tel)
{
   $kphone = preg_replace('/(.*), (.*)/', '$1', $tel);
   $kphone = str_replace([' ', '(', ')', '-'], '', $kphone);
   return $kphone;
}
/**
 * Возвращает значение между строк
 * @param $start - начало поиска
 * @param $end - конец поиска
 * @param $str - строка для поиска
 * @return string 
 */
function getInbetweenStrings($start, $end, $str)
{
   $matches = array();
   $regex = "/$start([a-zA-Z0-9_]*)$end/";
   preg_match_all($regex, $str, $matches);
   return $matches[1];
}
/**
 * Возвращает ссылку на преобразованную картинку
 * @param $photo - id картинки из таблицы b_file, либо массив описание файла
 * @param $width - ширина картинки
 * @param $height - высота картинки
 * @return string 
 */
function getResizePhoto($photo, $width, $height)
{
   $file = CFile::ResizeImageGet(
      $photo,
      array("width" => $width, "height" => $height),
      BX_RESIZE_IMAGE_PROPORTIONAL,
      true
   );
   return $file['src'];
}
/**
 * Возвращает строковое значение месяца
 * @param $num - число месяца
 * @return string
 */
function formatMonth($num)
{
   $arr = array(
      "01" => "января",
      "02" => "февраля",
      "03" => "марта",
      "04" => "апреля",
      "05" => "мая",
      "06" => "июня",
      "07" => "июля",
      "08" => "августа",
      "09" => "сентября",
      "10" => "октября",
      "11" => "ноября",
      "12" => "декабря"
   );
   return $arr[$num];
}
/**
 * Объект БитриксД7 _REQUEST
 * @return mixed
 */
function getRequestObject()
{
   return \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
}
/**
 * Возвращает объект менеджера управляемого кеша Bitrix D7
 *
 * @return object \Bitrix\Main\Data\ManagedCache
 */
function getCacheObj()
{
   return \Bitrix\Main\Application::getInstance()->getManagedCache();
}
/**
 * Проверяет является ли входной параметром ботом
 * @param $botname - название бота
 * @return boolean
 */
function isBot(&$botname = '')
{
   $bots = array(
      'rambler', 'googlebot', 'aport', 'yahoo', 'msnbot', 'turtle', 'mail.ru', 'omsktele',
      'yetibot', 'picsearch', 'sape.bot', 'sape_context', 'gigabot', 'snapbot', 'alexa.com',
      'megadownload.net', 'askpeter.info', 'igde.ru', 'ask.com', 'qwartabot', 'yanga.co.uk',
      'scoutjet', 'similarpages', 'oozbot', 'shrinktheweb.com', 'aboutusbot', 'followsite.com',
      'dataparksearch', 'google-sitemaps', 'appEngine-google', 'feedfetcher-google',
      'liveinternet.ru', 'xml-sitemaps.com', 'agama', 'metadatalabs.com', 'h1.hrn.ru',
      'googlealert.com', 'seo-rus.com', 'yaDirectBot', 'yandeG', 'yandex',
      'yandexSomething', 'Copyscape.com', 'AdsBot-Google', 'domaintools.com',
      'Nigma.ru', 'bing.com', 'dotnetdotcom'
   );
   foreach ($bots as $bot) {
      if (stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false) {
         $botname = $bot;
         return true;
      }
   }
   return false;
}
/**
 * Возвращает json формат массива
 * @param $array - массив для преобразования
 * @return string 
 */
function json_response($array)
{
   return \Bitrix\Main\Web\Json::encode($array);
}
