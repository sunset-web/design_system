<?
AddEventHandler("main", "OnBeforeEventAdd", array("CreateLeadB24", "createlead"));
class CreateLeadB24
{
   static function createlead(&$event, &$lid, &$arFields)
   {
      if ($event == 'FEEDBACK_FORM') {
         $name = trim($arFields['AUTHOR']);
         $phone = trim($arFields['user_phone']);
         $comment = trim($arFields['TEXT']);


         $roistatData = array(
            'roistat' => isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : 'nocookie',
            'key'     => 'keyb24', // Ключ для интеграции с CRM, указывается в настройках интеграции с CRM.
            'title'   => 'Заявка с сайта', // Название сделки
            'comment' => 'Тема:' . trim($arFields['user_title']) . '. ' . $comment . '<br><br>Страница: {landingPage}, Ист.ур1: {sourceAliasLevel1}, Ист.ур.2: {sourceAliasLevel2}, Ист.ур.3:{sourceAliasLevel3}, UTM: {utmSource}', // Комментарий к лиду
            'name'    => $name, // Имя клиента
            'email'   => '', // Email клиента
            'phone'   => $phone, // Номер телефона клиента
            'is_need_callback' => '0', // После того, как в Roistat создается заявка, Roistat инициирует обратный звонок на номер клиента, если значение параметра равно 1 и в Ловце лидов включен индикатор обратного звонка.
            'sync'    => '0', //
            'is_need_check_order_in_processing' => '1', // Включение проверки заявок на дубли
            'is_need_check_order_in_processing_append' => '1', // Если создана дублирующая заявка, в нее будет добавлен комментарий об этом
            'is_skip_sending' => '0', // Не отправлять заявку в CRM.
            'fields'  => array(
               // Массив дополнительных полей. Если дополнительные поля не нужны, оставьте массив пустым.
               // Примеры дополнительных полей смотрите в таблице ниже.
               // Помимо массива fields, который используется для сделки, есть еще массив client_fields, который используется для установки полей контакта.
               "charset" => "UTF-8", // Сервер преобразует значения полей из указанной кодировки в UTF-8.
            ),
         );

         file_get_contents("https://cloud.roistat.com/api/proxy/1.0/leads/add?" . http_build_query($roistatData));
      }
   }
}
