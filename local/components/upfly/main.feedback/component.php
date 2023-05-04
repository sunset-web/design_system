<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 * 
 * необходимо создать тип инфоблоков с кодом forms
 * под каждый тип формы создать инфоблок со свойствами
 * настроить созданные типы и инфоблоки в параметрах компонента
 * свойства подгружаются автоматически
 * в почтовом событии feedback_form сделать корректировки
 * прописать символьные кода свойств созданных инфоблоков
 */

$arResult["PARAMS_HASH"] = md5(serialize($arParams) . $this->GetTemplateName()); //hash компонента

$arResult["ERROR_MESSAGE"] = array(); //массив ошибок

//использование и генерация капчи
if (
	$arParams["USE_CAPTCHA"] == "Y" &&
	$_POST["captcha_sid"] &&
	$_POST["captcha_word"]
) {

	$captcha_code = $_POST["captcha_sid"];
	$captcha_word = $_POST["captcha_word"];

	$cpt = new CCaptcha();
	$captchaPass = COption::GetOptionString("main", "captcha_password", "");

	if ($captcha_word != '' && $captcha_code != '') {
		if (!$cpt->CheckCodeCrypt($captcha_word, $captcha_code, $captchaPass))
			$arResult["ERROR_MESSAGE"]['CAPTCHA'] = \Bitrix\Main\Localization\Loc::getMessage("MF_CAPTCHA_WRONG");
	} else
		$arResult["ERROR_MESSAGE"]['CAPTCHA'] = \Bitrix\Main\Localization\Loc::getMessage("MF_CAPTHCA_EMPTY");

	$arResult["capCode"] =  htmlspecialcharsbx($APPLICATION->CaptchaGetCode());
}


$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]); // тип инфоблока
if ($arParams["IBLOCK_TYPE"] == '')
	$arParams["IBLOCK_TYPE"] = "services";

$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]); // id инфоблока

$arParams["EVENT_NAME"] = trim($arParams["EVENT_NAME"]); // имя события
if ($arParams["EVENT_NAME"] == '')
	$arParams["EVENT_NAME"] = "FEEDBACK_FORM";

$arParams["EMAIL_TO"] = trim($arParams["EMAIL_TO"]); //почта для уведомления, если нет берем из главного модуля
if ($arParams["EMAIL_TO"] == '')
	$arParams["EMAIL_TO"] = COption::GetOptionString("main", "email_from");

$arParams["OK_TEXT"] = trim($arParams["OK_TEXT"]); // уведомление об успешной отправке
if ($arParams["OK_TEXT"] == '')
	$arParams["OK_TEXT"] = \Bitrix\Main\Localization\Loc::getMessage("MF_OK_MESSAGE");

$arParams["TITLE_FORM"] = html_entity_decode(trim($arParams["TITLE_FORM"])); // заголовок формы с html

$arParams["POLICY_TEXT"] = html_entity_decode(trim($arParams["POLICY_TEXT"])); // текст  политики с html и ссылкой
if ($arParams["POLICY_LINK"] != '')
	$arParams["POLICY_TEXT"] = str_replace('#LINK#', $arParams["POLICY_LINK"], $arParams["POLICY_TEXT"]);

$arResult["TITLE_FORM"] = $arParams["TITLE_FORM"];
$arResult["POLICY_TEXT"] = $arParams["POLICY_TEXT"];

$arParams["FIELDS"] = (!empty($arParams["FIELDS"]) ? array_diff($arParams["FIELDS"], ["NONE"]) : array()); // список полей
$arParams["REQUIRED_FIELDS"] = (!empty($arParams["FIELDS"]) ? array_diff($arParams["REQUIRED_FIELDS"], ["NONE"]) : array()); // список обязательных полей

// список полей
$arFilterProps = ['=ACTIVE' => "Y"];
if ($site !== false)
	$arFilterProps["=IBLOCK_ID"] = $arParams["IBLOCK_ID"];

$resultQueryProps = \Bitrix\Iblock\PropertyTable::getList([
	'filter' => $arFilterProps,
	'cache' => [
		'ttl' => 3600000,
		'cache_joins' => true,
	],
	'select'  =>  [
		'*'

	],
])->FetchAll();

$arProperty = [];
$arResult['REQUIRED_FIELDS'] = [];
foreach ($resultQueryProps as $item) {

	$arResult['FIELDS'][$item["CODE"]] = $item;

	// обязательные поля
	if ($item["IS_REQUIRED"] == "Y") {
		$arResult['REQUIRED_FIELDS'][] = $item["CODE"];
	}
	// телефон
	if ($item["USER_TYPE_SETTINGS_LIST"]['PHONE']) {
		$arResult['PHONE_FIELDS'][] = $item["CODE"];
	}
	// почта
	if ($item["USER_TYPE_SETTINGS_LIST"]['MAIL']) {
		$arResult['MAIL_FIELDS'][] = $item["CODE"];
	}
	// скрытое поле
	if ($item["USER_TYPE_SETTINGS_LIST"]['HIDDEN']) {
		$arResult['HIDDEN_FIELDS'][] = $item["CODE"];
	}
	// текстовое поле
	if ($item["USER_TYPE"] == "HTML") {
		$arResult['TEXT_FIELDS'][] = $item["CODE"];
	}
}

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

if ($request->getPost("submit") && $arResult["PARAMS_HASH"] === $request->getPost("PARAMS_HASH")) {
	if (check_bitrix_sessid()) { //проверяем сессию

		// защита от спама
		if ($request->getPost("user_security") != '') {

			foreach ($arResult['TEXT_FIELDS'] as $code) {

				if (
					stripos($request->getPost($code), '<br>') !== false || stripos($request->getPost($code), '</br>') !== false ||
					stripos($request->getPost($code), '</b>') !== false || stripos($request->getPost($code), 'href') !== false ||
					stripos($request->getPost($code), 'src') !== false || stripos($request->getPost($code), '<?xml') !== false
				) {

					$arResult["ERROR_MESSAGE"]['SECURITY_TEXT'] = \Bitrix\Main\Localization\Loc::getMessage("SECURITY_TEXT");
					break;
				}
			}
		} else {

			$arResult["ERROR_MESSAGE"]['SECURITY_TEXT'] = \Bitrix\Main\Localization\Loc::getMessage("SECURITY_TEXT");
		}

		// валидация телефона
		foreach ($arResult['PHONE_FIELDS'] as $code) {

			if (mb_strlen($request->getPost($code)) <= 12 || stripos($request->getPost($code), '_') !== false) {

				$arResult["ERROR_MESSAGE"][$code] = \Bitrix\Main\Localization\Loc::getMessage("MF_PHONE_NOT_VALID");
			}
		}
		// валидация почты
		foreach ($arResult['MAIL_FIELDS'] as $code) {

			if (!check_email($request->getPost($code))) {

				$arResult["ERROR_MESSAGE"][$code] = \Bitrix\Main\Localization\Loc::getMessage("MF_EMAIL_NOT_VALID");
			}
		}

		// валидация обязательных полей
		foreach ($arResult['REQUIRED_FIELDS'] as $code) {

			if (mb_strlen($request->getPost($code)) <= 2) {

				$arResult["ERROR_MESSAGE"][$code] = str_replace('#NAME#', mb_strtolower($arResult['FIELDS'][$code]['NAME']), \Bitrix\Main\Localization\Loc::getMessage("MF_REQ_TEXT"));
			}
		}

		// Если нет ошибок
		if (empty($arResult["ERROR_MESSAGE"])) {
			// массив данных для отправки
			// Заносим данные в сессию
			foreach ($arResult['FIELDS'] as $field) {

				$arFields[$field['CODE']] = $request->getPost($field['CODE']);
				$_SESSION[$field['CODE']] = htmlspecialcharsbx($request->getPost($field['CODE']));
			}

			// добавляем в инфоблок
			$elObj = new CIBlockElement;

			$arLoadProductArray = array(
				"IBLOCK_ID"      => $arParams["IBLOCK_ID"],
				"PROPERTY_VALUES" => $arFields,
				"NAME"           => "Заявка от " . date("Y-m-d H:i:s"),
				"ACTIVE"         => "Y",
			);

			$elObj->Add($arLoadProductArray);

			// Отправляем сообщения
			$arFields["EMAIL_TO"] = $arParams["EMAIL_TO"];
			if (!empty($arParams["EVENT_MESSAGE_ID"])) {

				foreach ($arParams["EVENT_MESSAGE_ID"] as $event)
					if (intval($event) > 0)
						\Bitrix\Main\Mail\Event::send([
							"EVENT_NAME" => $arParams["EVENT_NAME"],
							"MESSAGE_ID" => intval($event),
							"LID" => SITE_ID,
							"DUPLICATE" => "N",
							"C_FIELDS" => $arFields
						]);
			} else
				\Bitrix\Main\Mail\Event::send([
					"EVENT_NAME" => $arParams["EVENT_NAME"],
					"LID" => SITE_ID,
					"DUPLICATE" => "N",
					"C_FIELDS" => $arFields
				]);

			// Перенаправляем на страницу благодарности
			LocalRedirect($APPLICATION->GetCurPageParam("success=" . $arResult["PARAMS_HASH"], ["success"]));
		}

		foreach ($arResult['FIELDS'] as $field) {

			$arResult[$field['CODE']] = htmlspecialcharsbx($request->getPost($field['CODE']));
		}
	} else
		$arResult["ERROR_MESSAGE"][] = \Bitrix\Main\Localization\Loc::getMessage("MF_SESS_EXP"); //Истекла сессия
} elseif ($_REQUEST["success"] == $arResult["PARAMS_HASH"]) { // Если успешная отправка
	$arResult["OK_MESSAGE"] = $arParams["OK_TEXT"];
}


// Выводим значения, если нет ошибок
if (empty($arResult["ERROR_MESSAGE"])) {
	if ($USER->IsAuthorized()) { // для авторизованного из ЛК
		$arResult["user_name"] = $USER->GetFormattedName(false);
		$arResult["user_email"] = htmlspecialcharsbx($USER->GetEmail());
	} else { // для не авторизованного из сессии

		foreach ($arResult['FIELDS'] as $field) {
			if ($_SESSION[$field['CODE']] != '')
				$arResult[$field['CODE']] = htmlspecialcharsbx($request->getPost($field['CODE']));
		}
	}
}


$this->IncludeComponentTemplate();
