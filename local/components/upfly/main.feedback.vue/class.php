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

use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Contract\Controllerable;
use CBitrixComponent;

\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('main');

class MainFeedback extends CBitrixComponent implements Controllerable
{
  public $arParams;
  public $arResult;

  public function onPrepareComponentParams($arParams)
  {
    $this->arResult["PARAMS_HASH"] = md5(serialize($arParams) . $this->GetTemplateName()); //hash компонента

    $this->arResult["ERROR_MESSAGE"] = array(); //массив ошибок

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
          $this->arResult["ERROR_MESSAGE"]['CAPTCHA'] = \Bitrix\Main\Localization\Loc::getMessage("MF_CAPTCHA_WRONG");
      } else
        $this->arResult["ERROR_MESSAGE"]['CAPTCHA'] = \Bitrix\Main\Localization\Loc::getMessage("MF_CAPTHCA_EMPTY");

      $this->arResult["capCode"] =  htmlspecialcharsbx($APPLICATION->CaptchaGetCode());
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

    $this->arResult["TITLE_FORM"] = $arParams["TITLE_FORM"];
    $this->arResult["POLICY_TEXT"] = $arParams["POLICY_TEXT"];

    $arParams["FIELDS"] = (!empty($arParams["FIELDS"]) ? array_diff($arParams["FIELDS"], ["NONE"]) : array()); // список полей
    $arParams["REQUIRED_FIELDS"] = (!empty($arParams["FIELDS"]) ? array_diff($arParams["REQUIRED_FIELDS"], ["NONE"]) : array()); // список обязательных полей

    return $arParams;
  }

  public function executeComponent()
  {
    if ($this->startResultCache()) {
      // список полей
      $arFilterProps = ['=ACTIVE' => "Y"];
      $arFilterProps["=IBLOCK_ID"] = $this->arParams["IBLOCK_ID"];

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

      $this->arResult['REQUIRED_FIELDS'] = [];
      foreach ($resultQueryProps as $item) {

        $this->arResult['FIELDS'][$item["CODE"]] = $item;

        // обязательные поля
        if ($item["IS_REQUIRED"] == "Y") {
          $this->arResult['REQUIRED_FIELDS'][] = $item["CODE"];
        }
        // телефон
        if ($item["USER_TYPE_SETTINGS_LIST"]['PHONE']) {
          $this->arResult['PHONE_FIELDS'][] = $item["CODE"];
        }
        // почта
        if ($item["USER_TYPE_SETTINGS_LIST"]['MAIL']) {
          $this->arResult['MAIL_FIELDS'][] = $item["CODE"];
        }
        // скрытое поле
        if ($item["USER_TYPE_SETTINGS_LIST"]['HIDDEN']) {
          $this->arResult['HIDDEN_FIELDS'][] = $item["CODE"];
        }
        // текстовое поле
        if ($item["USER_TYPE"] == "HTML") {
          $this->arResult['TEXT_FIELDS'][] = $item["CODE"];
        }
      }

      $this->includeComponentTemplate();
    }
  }

  public function configureActions(): array
  {
    return [
      'send' => [
        'prefilters' => []
      ],
    ];
  }

  public function sendAction($data, $arResultForm, $arParamsForm)
  {
    try {
      if (check_bitrix_sessid()) { //проверяем сессию
        // защита от спама
        $arResultForm['ERROR_MESSAGE'] = json_decode($arResultForm['REQUIRED_FIELDS']);
        if ($data['input_security'] == '') {

          $arResultForm["ERROR_MESSAGE"]['SECURITY_TEXT'] = \Bitrix\Main\Localization\Loc::getMessage("SECURITY_TEXT");
        } else {
          // foreach ($arResultForm['REQUIRED_FIELDS'] as $code) {

          //   if ($data[$code] == '') {
          //     $arResultForm["ERROR_MESSAGE"]['SECURITY_TEXT'] = \Bitrix\Main\Localization\Loc::getMessage("SECURITY_TEXT");
          //     break;
          //   }
          // }
        }

        // валидация телефона
        foreach ($arResultForm['PHONE_FIELDS'] as $code) {
          if (mb_strlen($data[$code]) <= 12 || stripos($data[$code], '_') !== false) {
            $arResultForm["ERROR_MESSAGE"][$code] = \Bitrix\Main\Localization\Loc::getMessage("MF_PHONE_NOT_VALID");
          }
        }
        // валидация почты
        foreach ($arResultForm['MAIL_FIELDS'] as $code) {
          if (in_array($code, $arResultForm['REQUIRED_FIELDS'])) {
            if (!check_email($data[$code])) {
              $arResultForm["ERROR_MESSAGE"][$code] = \Bitrix\Main\Localization\Loc::getMessage("MF_EMAIL_NOT_VALID");
            }
          }
        }

        // валидация обязательных полей
        foreach ($arResultForm['REQUIRED_FIELDS'] as $code) {
          if (mb_strlen($data[$code]) <= 2) {
            $arResultForm["ERROR_MESSAGE"][$code] = str_replace('#NAME#', mb_strtolower($arResultForm['FIELDS'][$code]['NAME']), \Bitrix\Main\Localization\Loc::getMessage("MF_REQ_TEXT"));
          }
        }

        // Если нет ошибок
        if (empty($arResultForm["ERROR_MESSAGE"])) {

          // массив данных для отправки
          foreach ($arResultForm['FIELDS'] as $field) {
            $arFields[$field['CODE']] = $data[$field['CODE']];
          }

          // добавляем в инфоблок
          $elObj = new CIBlockElement;

          $arLoadProductArray = array(
            "IBLOCK_ID"      => $arParamsForm["IBLOCK_ID"],
            "PROPERTY_VALUES" => $arFields,
            "NAME"           => "Заявка от " . date("Y-m-d H:i:s"),
            "ACTIVE"         => "Y",
          );

          $success = $elObj->Add($arLoadProductArray);

          // Отправляем сообщения
          $arFields["EMAIL_TO"] = $arParamsForm["EMAIL_TO"];
          if (!empty($arParamsForm["EVENT_MESSAGE_ID"])) {
            foreach ($arParamsForm["EVENT_MESSAGE_ID"] as $event)
              if (intval($event) > 0)
                \Bitrix\Main\Mail\Event::send([
                  "EVENT_NAME" => $arParamsForm["EVENT_NAME"],
                  "MESSAGE_ID" => intval($event),
                  "LID" => SITE_ID,
                  "DUPLICATE" => "N",
                  "C_FIELDS" => $arFields
                ]);
          } else
            \Bitrix\Main\Mail\Event::send([
              "EVENT_NAME" => $arParamsForm["EVENT_NAME"],
              "LID" => SITE_ID,
              "DUPLICATE" => "N",
              "C_FIELDS" => $arFields
            ]);

          // Перенаправляем на страницу благодарности
          // LocalRedirect($APPLICATION->GetCurPageParam("success=" . $arResultForm["PARAMS_HASH"], ["success"]));
        }

        foreach ($arResultForm['FIELDS'] as $field) {

          $arResultForm[$field['CODE']] = htmlspecialcharsbx($data[$field['CODE']]);
        }
      } else $arResultForm["ERROR_MESSAGE"][] = \Bitrix\Main\Localization\Loc::getMessage("MF_SESS_EXP"); //Истекла сессия

      return [
        "err" => $arResultForm["ERROR_MESSAGE"],
        "success" => $success,
        "data" => $data,
      ];
    } catch (Exceptions\EmptyEmail $e) {
      $this->errorCollection[] = new Error($e->getMessage());
      return [
        "result" => $this->errorCollection,
      ];
    }
  }
}
