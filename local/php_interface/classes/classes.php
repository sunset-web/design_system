<?
// Создания лида в Б24 через отправку формы
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/integrations/CreateLeadB24.php');
// Подключение началных настроек из ИБ на загрузку страницы
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/events/LoadPage.php');
// метод модифицирует поисковый индекс для элементов и разделов инфоблока
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/events/SearchCatalog.php');
// Подмену логина на почту и телефон/ генерация автоматического логина
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/events/LoginPhoneMail.php');
// Доп свойство наследуемое для элемента
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/props/CIBlockNewPropertySEO.php');
// Доп свойство наследуемое для раздела
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/props/CIBlockNewPropertySectionSEO.php');
// Доп свойство для компонента main.feedback
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/props/SettingsPropertiyForm.php');
// Доп свойство привязка к элементам с описанием
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/props/UpflyPropertiesList.php');
// Доп свойство привязка к цене
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/props/UpflyPriceFilter.php');
// Доп свойство перекрестные продажи
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/props/UpflyCrossSell.php');
// Доп свойство условие фильтрации для перекрестных продаж
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/props/UpflyConditionMoreLess.php');
// Доп свойство склад для элементов
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/props/CIBlockNewPropertyStock.php');
// Доп свойство склад для разделов
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/props/CIBlockNewPropertyStockSection.php');
// Доп свойство привязка к медиабиблиотеке для разделов
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/props/PropMediaLibUserType.php');
// Доп свойство привязка к медиабиблиотеке для элементов
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/props/PropMediaLibIblockProperty.php');
// Доп свойство привязка к компаниям из CRM
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/props/CIBlockPropertyCRM.php');

//Load classes
\Bitrix\Main\Loader::registerAutoLoadClasses(
   null,
   array(
      "ImportFns"  => "/local/php_interface/classes/integrations/ImportFns.php", // интеграция с ФНС
      "ItinApi"  => "/local/php_interface/classes/integrations/CustomApi.php", // собственный Апи
      "ImportHh"  => "/local/php_interface/classes/integrations/ImportHh.php", // интеграция с hh.ru
      "Dadata"  => "/local/php_interface/classes/integrations/Dadata.php", // интеграция с dadata
      "CurrencyCbr"  => "/local/php_interface/classes/integrations/CurrencyCbr.php", // интеграция с центральным банком России
      "ImportCrm"  => "/local/php_interface/classes/integrations/ImportCrm.php", // интеграция с b24
      "Sms"  => "/local/php_interface/classes/integrations/Sms.php", // интеграция с смс-сервисом
      "CRest"  => "/local/php_interface/classes/lib/crest/crest.php", // библиотека crest

      "CalculateGeolocation"  => "/local/php_interface/classes/lib/CalculateGeolocation.php", // рассчет расстояния между точками через координаты
      "ExportController"  => "/local/php_interface/classes/lib/export-import.upfly/ExportController.php", // экспорт елементов в exel
      "ImportController"  => "/local/php_interface/classes/lib/export-import.upfly/ImportController.php", // импорт элементов из exel
      "YoutubeVideo"  => "/local/php_interface/classes/lib/YoutubeVideo.php", // работа ссылкой youtube
      "GoogleDriveVideo"  => "/local/php_interface/classes/lib/GoogleDriveVideo.php", // работа ссылкой googledrive
      "FavoritesManager"  => "/local/php_interface/classes/lib/FavoritesManager.php", // класс добавления в избранное
   )
);
