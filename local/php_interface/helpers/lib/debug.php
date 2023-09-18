<?
function debug_var_dump($context)
{
   echo '<pre>';
   var_dump($context);
   echo '</pre>';
}

function debug_print_r($context)
{
   echo '<pre>';
   print_r($context);
   echo '</pre>';
}

function debug_get_var_dump($context)
{
   if ($_GET['debug'] = 'y') {
      echo '<pre>';
      var_dump($context);
      echo '</pre>';
   }
}

function debug_get_print_r($context)
{
   if ($_GET['debug'] = 'y') {
      echo '<pre>';
      print_r($context);
      echo '</pre>';
   }
}

function debug_bitrix_log($context)
{
   CEventLog::Add(array(
      "MODULE_ID" => "debug",
      "DESCRIPTION" => json_encode($context),
   ));
}
