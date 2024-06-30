<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;

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
			|| !Loader::includeModule('sale')
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
			]
		];
	}

	/**
	 * Получение списка метод
	 * @param $arParams
	 * @return mixed
	 */
	public function getList($id, $offset)
	{


		return;
	}

	/**
	 * Экшен обрабатываемого метода из runComponentAction
	 */
	public function getListAction($unit, $offset)
	{
		try {
			return [
				"result" => self::getList($unit, $offset),
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
