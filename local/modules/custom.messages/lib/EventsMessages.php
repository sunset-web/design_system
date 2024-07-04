<?php

namespace Custom\Messages;

use \Bitrix\Main\Application;
use \Bitrix\Main\Entity\Event;
use \Bitrix\Highloadblock\HighloadBlockTable;
use \Bitrix\Main\Mail\Event as Mail;


/*
 * Пояснения:
 * Класс выполняет очистку кеша на события добавления, изменения, удаления собщения, а так же отправку уведомления о новом сообщении администратору
 */

class EventsMessages
{
	// Очистка тегированного кеша
	private static function clearCache($id)
	{
		global $CACHE_MANAGER;
		$CACHE_MANAGER->clearByTag('HLBLOCK_' . $id);
	}
	// Получение айди текущего сайта
	private static function getSiteId()
	{
		$rsSites = \CSite::GetList($by = "sort", $order = "desc", array("DOMAIN" => $_SERVER['SERVER_NAME']))->Fetch();
		return $rsSites["LID"];
	}
	// Получение пользователя по id
	private static function getUserStringById($id)
	{
		$result = \Bitrix\Main\UserTable::getList(array(
			'select' => array('ID', 'NAME', 'LAST_NAME', 'LOGIN'),
			'filter' => array('=ID' => (int) $id)
		))->Fetch();

		return $result['LAST_NAME'] . ' ' . $result['NAME'] . '(' . $result['LOGIN'] . ')';
	}
	// Получение id сущности
	private static function getIdEntity($tableName)
	{
		$hlblock = HighloadBlockTable::getList(
			array("filter" => array(
				'TABLE_NAME' => $tableName
			))
		)->fetch();

		return $hlblock['ID'];
	}
	// Действия после добавления
	public static function add(Event $event)
	{
		$fields = $event->getParameter("fields");
		Mail::send([
			"EVENT_NAME" => "NEW_MESSAGE",
			"LID" => self::getSiteId(),
			"DUPLICATE" => "N",
			"C_FIELDS" => [
				"USER" => self::getUserStringById($fields['UF_USER']),
				"TEXT" => $fields['UF_TEXT'],
			]
		]);
		$entity = $event->getEntity();
		self::clearCache(self::getIdEntity($entity->getDBTableName()));
	}
	// Действия после изменения
	public static function update(Event $event)
	{
		$entity = $event->getEntity();
		self::clearCache(self::getIdEntity($entity->getDBTableName()));
	}
	// Действия после удаления
	public static function delete(Event $event)
	{
		$entity = $event->getEntity();
		self::clearCache(self::getIdEntity($entity->getDBTableName()));
	}
}
