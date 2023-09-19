<?
// Модификация 404 ошибки
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/helpers/lib/modifi404page.php');
// Дебаг ошибок (гет-параметр debug=y)
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/helpers/lib/debug.php');
// Получение настроек из модуля askaron.settings
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/helpers/lib/settings.php');
// Прочие хелперы
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/helpers/lib/others.php');
// Хелперы для CustomApi
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/helpers/lib/customapi.php');
// Хелперы на автоматическое обновление в публичной части
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/helpers/lib/pull.php');
