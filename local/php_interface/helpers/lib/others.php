<?
// форматирование телефона
function formatPhone($tel)
{
   $kphone = preg_replace('/(.*), (.*)/', '$1', $tel);
   $kphone = str_replace([' ', '(', ')', '-'], '', $kphone);
   return $kphone;
}
// получение значения между строк
function getInbetweenStrings($start, $end, $str)
{
   $matches = array();
   $regex = "/$start([a-zA-Z0-9_]*)$end/";
   preg_match_all($regex, $str, $matches);
   return $matches[1];
}
// преобразование картинок
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
