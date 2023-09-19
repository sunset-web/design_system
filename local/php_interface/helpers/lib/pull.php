<?
// Подписка на модуль при добавлении новости/уведомления
function notificAdd(\Bitrix\Main\Entity\Event $event)
{

   if (!CModule::IncludeModule('pull'))
      return false;

   // получаем необходимые поля
   $arFields = $event->getParameter("fields");
   $id = $event->getParameter("id");
   // Преобразование полей
   $arFields['UF_COMPANY'] = unserialize($arFields['UF_COMPANY']) ? unserialize($arFields['UF_COMPANY']) : $arFields['UF_COMPANY'];
   $arFields['UF_DATE'] = $arFields['UF_DATE']->format("Y.m.d в H:i");
   // получение информации по hl-блоку
   $entity = $event->getEntity();
   $entityTable = $entity->getDBTableName();
   $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(
      array("filter" => array(
         'TABLE_NAME' => $entityTable
      ))
   )->fetch();

   $status = CPullOptions::GetNginxStatus();
   CPullStack::AddShared(array(
      'module_id' => 'droplistheader',
      'command' => 'check',
      'params' =>
      array(
         'status' => $status,
         'response' => array(
            'hl_id' => $hlblock['ID'],
            'fields' => $arFields,
            'id' => $id,
         ),
      ),
   ));
}

// Подписка на модуль при добавлении новости/уведомления
function notificUpdate(\Bitrix\Main\Entity\Event $event)
{

   if (!CModule::IncludeModule('pull'))
      return false;

   // получаем необходимые поля
   $arFields = $event->getParameters('fields');
   $id = $event->getParameter("id");
   // получение информации по hl-блоку
   $entity = $event->getEntity();
   $entityTable = $entity->getDBTableName();
   $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(
      array("filter" => array(
         'TABLE_NAME' => $entityTable
      ))
   )->fetch();

   $status = CPullOptions::GetNginxStatus();
   CPullStack::AddShared(array(
      'module_id' => 'droplistheader',
      'command' => 'update',
      'params' =>
      array(
         'status' => $status,
         'response' => array(
            'hl_id' => $hlblock['ID'],
            'view' => $arFields['fields']['UF_VIEW'],
            'id' => $id['ID'],
         ),
      ),
   ));
}
