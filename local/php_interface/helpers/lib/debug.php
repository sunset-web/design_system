<?

/**
 * Возвращает отформатированный контекст функцией var_dump
 * @param $context - контекст для вывода
 * @return string 
 */
function debug_var_dump($context)
{
   echo '<pre>';
   var_dump($context);
   echo '</pre>';
}
/**
 * Возвращает отформатированный контекст функцией print_r
 * @param $context - контекст для вывода
 * @return string 
 */
function debug_print_r($context)
{
   echo '<pre>';
   print_r($context);
   echo '</pre>';
}
/**
 * Возвращает отформатированный контекст функцией var_dump под гет параметром debug=y
 * @param $context - контекст для вывода
 * @return string 
 */
function debug_get_var_dump($context)
{
   if ($_GET['debug'] == 'y') {
      echo '<pre>';
      var_dump($context);
      echo '</pre>';
   }
}
/**
 * Возвращает отформатированный контекст функцией print_r под гет параметром debug=y
 * @param $context - контекст для вывода
 * @return string 
 */
function debug_get_print_r($context)
{
   if ($_GET['debug'] == 'y') {
      echo '<pre>';
      print_r($context);
      echo '</pre>';
   }
}
/**
 * Записывает контекст в журнал событий
 * @param $context - контекст для вывода
 */
function debug_bitrix_log($context)
{
   CEventLog::Add(array(
      "MODULE_ID" => "debug",
      "DESCRIPTION" => json_encode($context),
   ));
}