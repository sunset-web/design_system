<?
// Подключение началных настроек из ИБ на загрузку страницы
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/classes/lib/LoadPage.php');
// метод модифицирует поисковый индекс для элементов и разделов инфоблока
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/classes/lib/SearchCatalog.php');
// Доп свойство для компонента main.feedback
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/classes/lib/SettingsPropertiyForm.php');
// Доп свойство привязка к элементам с описанием
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/classes/lib/UpflyPropertiesList.php');
// Создания лида в Б24 через отправку формы
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/classes/lib/CreateLeadB24.php');

//Load classes
\Bitrix\Main\Loader::registerAutoLoadClasses(
   null,
   array(
      "ImportHh"  => "/bitrix/php_interface/classes/integrations/ImportHh.php",
      "Dadata"  => "/bitrix/php_interface/classes/integrations/Dadata.php",
      "ImportCrm"  => "/bitrix/php_interface/classes/integrations/ImportCrm.php",
      "CRest"  => "/bitrix/php_interface/classes/integrations/crest/crest.php",
   )
);
