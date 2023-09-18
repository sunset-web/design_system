<?
AddEventHandler("main", "OnPageStart", array("LoadPage", "GetSettings"));
// Класс реализует подключение общих настроек сайта
class LoadPage
{
    // Получение значения настроек
    function GetSettings()
    {
        global $arrMainSettings;

        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $arrMainSettings = \Bitrix\Iblock\Elements\ElementMainSettingsTable::getByPrimary(0, [
                'select' => ['TIME_' => 'TIME', 'EMAIL_' => 'EMAIL', 'ADDRESS_' => 'ADDRESS', 'PHONE_' => 'PHONE',],
                "cache" => ["ttl" => 86400],
            ])->fetch();
        }
    }
}
