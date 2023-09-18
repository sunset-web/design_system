<?
// Подключение началных настроек из ИБ на загрузку страницы
// require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/classes/lib/LoadPage.php');


//Load classes
\Bitrix\Main\Loader::registerAutoLoadClasses(
   null,
   array(
      // "ImportHh"  => "/bitrix/php_interface/classes/integrations/ImportHh.php",
      // "Dadata"  => "/bitrix/php_interface/classes/integrations/Dadata.php",
   )
);
