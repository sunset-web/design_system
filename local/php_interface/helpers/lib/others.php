<?

/**
 * Возвращает отформатированное значение телефона
 * @param $tel - телефон для форматирования
 * @return string 
 */
function formatPhone($tel)
{
   if (preg_match('~[0-9]+~', $tel)) {
      $kphone = preg_replace('/(.*), (.*)/', '$1', $tel);
      $kphone = str_replace([' ', '(', ')', '-'], '', $kphone);
      return $kphone;
   }

   return false;
}
/**
 * Возвращает значение между строк (массивом)
 * @param $start - начало поиска
 * @param $end - конец поиска
 * @param $str - строка для поиска
 * @return string 
 */
function getInbetweenStrings($start, $end, $str)
{
   $matches = array();
   $regex = "/$start([a-zA-Z0-9_]*)$end/";
   if ($str) {
      preg_match_all($regex, $str, $matches);
      return $matches[1];
   }

   return false;
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
   if ($file) {
      return $file['src'];
   }
   return false;
}
/**
 * Возвращает строковое значение месяца
 * @param $num - число месяца
 * @return string
 */
function formatMonth($num)
{
   $num = +$num;
   $arr = array(
      "1" => "января",
      "2" => "февраля",
      "3" => "марта",
      "4" => "апреля",
      "5" => "мая",
      "6" => "июня",
      "7" => "июля",
      "8" => "августа",
      "9" => "сентября",
      "10" => "октября",
      "11" => "ноября",
      "12" => "декабря"
   );
   if ($num >= 1 && $num <= 12) {
      return $arr[$num];
   }
   return false;
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
/**
 * Возвращает класс хайлоад блока для дальнейших взаимодействий
 * @param $id - id хайлоад блока
 * @return string 
 */
function getHLClassFromId($id)
{
   $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($id)->fetch();
   $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
   return $entity->getDataClass();
}
/**
 * Возвращает правильное окончание слова в зависимости от числа, которому оно сопоставлено
 *
 * @param int $count
 * @param string $form1
 * @param string $form2_4
 * @param string $form5_0
 *
 * @return string
 */
function wordEnding($count, $form1 = "", $form2_4 = "а", $form5_0 = "ов")
{
   $n100 = $count % 100;
   $n10 = $count % 10;

   if (($n100 > 10) && ($n100 < 21)) {
      return $form5_0;
   } else if ((!$n10) || ($n10 >= 5)) {
      return $form5_0;
   } else if ($n10 == 1) {
      return $form1;
   }

   return $form2_4;
}
/**
 * @param $email
 * @return false|int
 *
 * Проверка E-mail
 */
function checkEmail($email)
{
   return preg_match('|^[_a-z0-9:()-]+(\.[_a-z0-9:()-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$|i', $email);
}
