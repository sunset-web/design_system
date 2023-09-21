<?php

use \Bitrix\Main\Loader;

use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Contract\Controllerable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class ExampleCompSimple extends CBitrixComponent implements Controllerable, Errorable
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
			!Loader::includeModule('iblock')
			|| !Loader::includeModule('pull')
			|| !Loader::includeModule('highloadblock')
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
		global $USER;
		return $USER;
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
			'getList' => [
				'prefilters' => [
					new ActionFilter\Authentication(), // проверяет авторизован ли пользователь
				]
			],
			'getListAnalogues' => [
				'prefilters' => [
					new ActionFilter\Authentication(), // проверяет авторизован ли пользователь
				]
			],
			'getListChanges' => [
				'prefilters' => [
					new ActionFilter\Authentication(), // проверяет авторизован ли пользователь
				]
			]
		];
	}

	/**
	 * Получение компаний по текущему пользователю
	 */
	private function getCompanyByUser()
	{
		$user = $this->_user();

		$elements = \Bitrix\Iblock\Elements\ElementCompaniesTable::getList([
			'select' => ['ID', 'PERSON_COMP_' => 'PERSON_COMP'],
			'filter' => ['=ACTIVE' => 'Y', 'PERSON_COMP_VALUE' => $user->GetID()],
			"cache" => ["ttl" => 3600],
		])->fetchAll();

		return array_column($elements, "ID");
	}

	/**
	 * Получение списка
	 */
	private function getList($hl_id, $count_page, $current_page)
	{
		$this->_checkModules();

		$companyIds = $this->getCompanyByUser();

		$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($hl_id)->fetch();
		$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
		$entity_data_class = $entity->getDataClass();

		$filter = ['=UF_COMPANY' => $companyIds];

		// Если не задано кол-во на странице
		if (!$count_page) {

			$count_page = 11;
			$filter['=UF_VIEW'] = 'false';
		}

		$listObj = $entity_data_class::getList(array(
			"select" => array("*"),
			"order" => array('ID' => "DESC"),
			"filter" => $filter,
			'offset' => $this->getOffset($count_page, $current_page),
			'limit' => $count_page,
		));

		$items = [];
		while ($item = $listObj->fetch()) {
			$item['UF_DATE'] = $item['UF_DATE']->format("Y.m.d в H:i");
			$items[] = $item;
		}
		return $items;
	}
	/**
	 * Получение смещения стр
	 */
	private function getOffset($count_page, $current_page)
	{
		return ($current_page - 1) * $count_page;
	}
	/**
	 * Получение кол-ва
	 */
	private function getCount($hl_id, $filter = array())
	{
		$this->_checkModules();

		$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($hl_id)->fetch();
		$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
		$entity_data_class = $entity->getDataClass();

		$totalCount = $entity_data_class::getList(array(
			"select" => array("ID"),
			'count_total' => true,
			"order" => array('ID' => "DESC"),
			"filter" => $filter,
		))->getCount();

		return $totalCount;
	}

	public function getListAction($hl_id, $count_page = 0, $current_page = 1)
	{
		try {
			$this->_checkModules();
			$companyIds = $this->getCompanyByUser();
			return [
				"result" => self::getList($hl_id, $count_page, $current_page),
				"count" => $count_page ? self::getCount($hl_id, ['=UF_COMPANY' => $companyIds]) : 0,
			];
		} catch (Exceptions\EmptyEmail $e) {
			$this->errorCollection[] = new Error($e->getMessage());
			return [
				"result" => "Произошла ошибка",
			];
		}
	}
	/**
	 * Получение списка аналогов
	 */
	private function getListAnalogues($hl_id, $count_page, $current_page, $id)
	{
		$this->_checkModules();

		$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($hl_id)->fetch();
		$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
		$entity_data_class = $entity->getDataClass();

		$filter = ['=UF_ANALOGUE' => $id];
		// Если не задано кол-во на странице
		if (!$count_page) {

			$count_page = 11;
		}

		$listObj = $entity_data_class::getList(array(
			"select" => array("*"),
			"order" => array('ID' => "DESC"),
			"filter" => $filter,
			'offset' => $this->getOffset($count_page, $current_page),
			'limit' => $count_page,
		));

		$items = [];
		while ($item = $listObj->fetch()) {
			$items[] = $item;
		}
		return $items;
	}
	public function getListAnaloguesAction($hl_id, $id, $count_page = 0, $current_page = 1)
	{
		try {
			return [
				"result" => self::getListAnalogues($hl_id, $count_page, $current_page, $id),
				"count" => $count_page ? self::getCount($hl_id, ['=UF_ANALOGUE' => $id]) : 0,
			];
		} catch (Exceptions\EmptyEmail $e) {
			$this->errorCollection[] = new Error($e->getMessage());
			return [
				"result" => "Произошла ошибка",
			];
		}
	}
	/**
	 * Получение списка аналогов
	 */
	private function getListChanges($hl_id, $count_page, $current_page, $id)
	{
		$this->_checkModules();

		$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($hl_id)->fetch();
		$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
		$entity_data_class = $entity->getDataClass();

		$filter = ['=UF_ELEMENT' => $id];
		// Если не задано кол-во на странице
		if (!$count_page) {

			$count_page = 11;
		}

		$listObj = $entity_data_class::getList(array(
			"select" => array("*"),
			"order" => array('ID' => "DESC"),
			"filter" => $filter,
			'offset' => $this->getOffset($count_page, $current_page),
			'limit' => $count_page,
		));

		$items = [];
		while ($item = $listObj->fetch()) {
			$items[] = $item;
		}
		return $items;
	}
	public function getListChangesAction($hl_id, $id, $count_page = 0, $current_page = 1)
	{
		try {
			return [
				"result" => self::getListChanges($hl_id, $count_page, $current_page, $id),
				"count" => $count_page ? self::getCount($hl_id, ['=UF_ELEMENT' => $id]) : 0,
			];
		} catch (Exceptions\EmptyEmail $e) {
			$this->errorCollection[] = new Error($e->getMessage());
			return [
				"result" => "Произошла ошибка",
			];
		}
	}
	/**
	 * Получение всех элементов
	 */
	private function getListAll($hl_id)
	{
		$this->_checkModules();

		$companyIds = $this->getCompanyByUser();

		$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($hl_id)->fetch();
		$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
		$entity_data_class = $entity->getDataClass();

		$filter = ['=UF_COMPANY' => $companyIds, '=UF_VIEW' => 'false'];

		$ids = $entity_data_class::getList(array(
			"select" => array("ID"),
			"order" => array('ID' => "DESC"),
			"filter" => $filter,
		))->fetchAll();

		return $ids;
	}
	/**
	 * Обновление просмотра уведомления
	 */
	private function update($hl_id, $ids)
	{

		$this->_checkModules();

		if (empty($ids)) {
			$ids = $this->getListAll($hl_id);
		}

		$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($hl_id)->fetch();
		$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
		$entity_data_class = $entity->getDataClass();

		foreach ($ids as $value) {
			$entity_data_class::update($value, array('UF_VIEW' => 'true'));
		}

		return true;
	}
	public function updateAction($hl_id, $ids = [])
	{
		try {
			return [
				"result" => self::update($hl_id, $ids),
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
	 * Должна содержать только последовательность вызовов вспомогательых ф-ий и минимум логики
	 * всю логику стараемся разносить по классам и методам
	 */
	public function executeComponent()
	{
		$this->_checkModules();

		if ($this->request->isPost()) {
			// some post actions
		}

		// some actions
		$this->arResult['SOME_VAR'] = 'some result data for template';

		$this->includeComponentTemplate();
	}
}
