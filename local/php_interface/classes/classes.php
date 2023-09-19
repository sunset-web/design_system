<?
// Подключение началных настроек из ИБ на загрузку страницы
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/classes/lib/LoadPage.php');
// метод модифицирует поисковый индекс для элементов и разделов инфоблока
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/classes/lib/SearchCatalog.php');
// Создания лида в Б24 через отправку формы
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/classes/lib/CreateLeadB24.php');
// Подмену логина на почту и телефон/ генерация автоматического логина
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/classes/lib/LoginPhoneMail.php');
// Доп свойство наследуемое для элемента
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/classes/lib/CIBlockNewPropertySEO.php');
// Доп свойство наследуемое для раздела
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/classes/lib/CIBlockNewPropertySectionSEO.php');
// Доп свойство для компонента main.feedback
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/classes/lib/SettingsPropertiyForm.php');
// Доп свойство привязка к элементам с описанием
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/classes/lib/UpflyPropertiesList.php');
// Доп свойство привязка к цене
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/classes/lib/UpflyPriceFilter.php');
// Доп свойство перекрестные продажи
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/classes/lib/UpflyCrossSell.php');
// Доп свойство условие фильтрации для перекрестных продаж
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/classes/lib/UpflyConditionMoreLess.php');

//Load classes
\Bitrix\Main\Loader::registerAutoLoadClasses(
   null,
   array(
      "ImportHh"  => "/bitrix/php_interface/classes/integrations/ImportHh.php", // интеграция с hh.ru
      "Dadata"  => "/bitrix/php_interface/classes/integrations/Dadata.php", // интеграция с dadata
      "ImportCrm"  => "/bitrix/php_interface/classes/integrations/ImportCrm.php", // интеграция с b24
      "CRest"  => "/bitrix/php_interface/classes/integrations/crest/crest.php", // библиотека crest
   )
);
