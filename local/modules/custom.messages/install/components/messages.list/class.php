<?php

use \Bitrix\Main\Loader;

use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\UserTable;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Application;
use Bitrix\Main\Component\ParameterSigner;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class MessageList extends CBitrixComponent implements Controllerable, Errorable
{

	protected ErrorCollection $errorCollection;

	/**
	 * Component constructor.
	 * @param CBitrixComponent | null $component
	 */
	public function __construct($component = null)
	{
		parent::__construct($component);
	}
	/**
	 * Требуется для работы Errorable
	 */
	public function getErrorByCode($code): Error
	{
		return $this->errorCollection->getErrorByCode($code);
	}
	public function getErrors(): array
	{
		return $this->errorCollection->toArray();
	}

	/**
	 * Проверка наличия модулей требуемых для работы компонента
	 * @return bool
	 * @throws Exception
	 */
	private function _checkModules()
	{
		if (
			!Loader::includeModule('highloadblock')
		) {
			throw new \Exception('Не загружены модули необходимые для работы модуля');
		}

		return true;
	}

	/**
	 * Обертка над глобальной переменной
	 * @return CAllMain|CMain
	 */
	private function _app()
	{
		global $APPLICATION;
		return $APPLICATION;
	}

	/**
	 * Обертка над глобальной переменной
	 * @return CAllUser|CUser
	 */
	private function _user()
	{
		return CurrentUser::get();
	}

	/**
	 * Подготовка параметров компонента
	 * @param $arParams
	 * @return mixed
	 */
	public function onPrepareComponentParams($arParams)
	{
		// тут пишем логику обработки параметров, дополнение параметрами по умолчанию
		// и прочие нужные вещи
		$this->errorCollection = new ErrorCollection();
		return $arParams;
	}

	/**
	 * Создание префильтров
	 */

	public function configureActions(): array
	{
		return [
			'delete' => [
				'prefilters' => [
					new ActionFilter\Authentication(), // проверяет авторизован ли пользователь
				]
			],
			'update' => [
				'prefilters' => [
					new ActionFilter\Authentication(), // проверяет авторизован ли пользователь
				]
			],
		];
	}

	/**
	 * Получение списка
	 */
	private function getList()
	{
		$this->_checkModules();
		try {
			// Проверяем на наличие телефона
			$arSelect = $this->arParams['FIELDS'];
			$arSelect = array_merge($arSelect, array('ID', 'USER_NAME' => 'buser.NAME', 'USER_LAST_NAME' => 'buser.LAST_NAME'));

			// инициализация навигации
			$nav = new PageNavigation("pagination");
			$nav->allowAllRecords(true)->setPageSize($this->arParams['PAGE_ELEMENT_COUNT'])->initFromUri();

			$hlblock = HighloadBlockTable::getById($this->arParams['HLBLOCKS_ID'])->fetch();
			$entity = HighloadBlockTable::compileEntity($hlblock);
			$entity_data_class = $entity->getDataClass();

			$result = $entity_data_class::getList(array(
				"select" => $arSelect,
				'offset' => $nav->getOffset(),
				'limit' => $nav->getLimit(),
				'count_total' => true,
				"order" => array('ID' => "DESC"),
				'runtime' => [
					'buser' => [
						'data_type' => UserTable::getEntity(),
						'reference' => [
							'=this.UF_USER' => 'ref.ID'
						]
					],
				]
			));

			// получаем число записей
			$nav->setRecordCount($result->getCount());

			$items = [];
			$currentuser = self::_user();
			while ($arUser = $result->fetch()) {
				// Флаг на изменение
				if ($currentuser->getId() == (int) $arUser['UF_USER'] || in_array(1, $currentuser->getUserGroups())) {
					$arUser['CHANGE_FLAG'] = 'Y';
				}
				// Флаг на удаление
				if (in_array(1, $currentuser->getUserGroups())) {
					$arUser['DELETE_FLAG'] = 'Y';
				}
				$items[] = $arUser;
			}
		} catch (Exception $e) {
			$this->errorCollection[] = new Error($e->getMessage());
			$cache = Cache::createInstance();
			$taggedCache = Application::getInstance()->getTaggedCache();
			$taggedCache->endTagCache();
			$cache->endDataCache($this->arResult);
		}
		return [
			'MESSAGES' => $items,
			'NAV' => $nav,
		];
	}

	/**
	 * Удаление элемента
	 */
	private function delete($id, $HLBLOCKS_ID)
	{

		$this->_checkModules();

		$hlblock = HighloadBlockTable::getById($HLBLOCKS_ID)->fetch();
		$entity = HighloadBlockTable::compileEntity($hlblock);
		$entity_data_class = $entity->getDataClass();

		$res = $entity_data_class::delete($id);

		return $res->isSuccess();
	}
	public function deleteAction($param)
	{
		try {
			$sign = ParameterSigner::unsignParameters($this->getName() . '_custom', $param);
			if (empty($sign['ID'])) $this->errorCollection[] = new Error($e->getMessage());

			return [
				"result" => self::delete($sign['ID'], $sign['HLBLOCKS_ID']),
			];
		} catch (Exceptions\EmptyEmail $e) {
			$this->errorCollection[] = new Error($e->getMessage());
			return [
				"result" => "Произошла ошибка",
			];
		}
	}
	/**
	 * Изменение элемента
	 */
	private function update($id, $HLBLOCKS_ID, $text)
	{

		$this->_checkModules();

		$hlblock = HighloadBlockTable::getById($HLBLOCKS_ID)->fetch();
		$entity = HighloadBlockTable::compileEntity($hlblock);
		$entity_data_class = $entity->getDataClass();

		$res = $entity_data_class::update($id, array('UF_TEXT' => $text));

		return $res->isSuccess();
	}
	public function updateAction($param, $text)
	{
		try {
			$sign = ParameterSigner::unsignParameters($this->getName() . '_custom', $param);
			if (empty($sign['ID'])) $this->errorCollection[] = new Error($e->getMessage());

			return [
				"result" => self::update($sign['ID'], $sign['HLBLOCKS_ID'], $text),
			];
		} catch (Exceptions\EmptyEmail $e) {
			$this->errorCollection[] = new Error($e->getMessage());
			return [
				"result" => "Произошла ошибка",
			];
		}
	}
	/**
	 * Точка входа в компонент
	 */
	public function executeComponent()
	{
		try {
			if ($this->arParams['CACHE_TYPE'] == 'A') {
				$cache = Cache::createInstance();
				$taggedCache = Application::getInstance()->getTaggedCache();

				$currentuser = self::_user();

				$cachePath = 'custom_messages';
				$cacheTtl = 3600;
				$cacheKey = 'HLBLOCK_' . $this->arParams['HLBLOCKS_ID'] . '-' . $this->request->getQuery('pagination') . '-' . $currentuser->getId();

				if ($cache->initCache($this->arParams['CACHE_TIME'], $cacheKey, $cachePath)) {
					$this->arResult = $cache->getVars();
				} elseif ($cache->startDataCache()) {
					$taggedCache->startTagCache($cachePath);

					$this->arResult = self::getList();

					$taggedCache->registerTag('HLBLOCK_' . $this->arParams['HLBLOCKS_ID']);

					$taggedCache->endTagCache();
					$cache->endDataCache($this->arResult);
				}
			} else {
				$this->arResult = self::getList();
			}
			$this->includeComponentTemplate();
		} catch (Exception $e) {
			$this->errorCollection[] = new Error($e->getMessage());
			$cache = Cache::createInstance();
			$taggedCache = Application::getInstance()->getTaggedCache();
			$taggedCache->endTagCache();
			$cache->endDataCache($this->arResult);
		}
	}
}
