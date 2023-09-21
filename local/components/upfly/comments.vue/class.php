<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Contract\Controllerable;
use CBitrixComponent;

use Bitrix\Main\Loader;

Loader::includeModule("highloadblock");
Loader::includeModule('iblock');
Loader::includeModule('pull');

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

class CompanyCreate extends CBitrixComponent implements Controllerable, Errorable
{
  protected ErrorCollection $errorCollection;
  public $arParams;


  public function onPrepareComponentParams($arParams)
  {
    $this->errorCollection = new ErrorCollection();
    return $arParams;
  }

  public function getErrors(): array
  {
    return $this->errorCollection->toArray();
  }

  public function getErrorByCode($code): Error
  {
    return $this->errorCollection->getErrorByCode($code);
  }

  function getOld($id, $offset)
  {
    $hlbl = 4;
    $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    $commentsObj = $entity_data_class::getList(array(
      "select" => array("*"),
      "order" => array('ID' => "DESC"),
      "filter" => array('UF_UNIT' => $id),
      "offset" => $offset,
      'limit' => 10,
    ));
    $comments = [];
    while ($comment = $commentsObj->fetch()) {
      $date = $comment['UF_TIME']->format("Y.m.d в H:i");
      $text = $comment['UF_COMMENT'];
      $user = \Bitrix\Main\UserTable::getList(array(
        'select' => array('ID', 'SHORT_NAME'),
        'order' => array(),
        'filter' => array(
          '=ID' => $comment['UF_USER'],
        ),
      ))->fetch();
      $comments[] = array(
        "date" => $date,
        "text" => $text,
        "user" => $user,
      );
    }
    return array_reverse($comments);
  }

  public function executeComponent()
  {
    if ($this->startResultCache()) {
      $this->arResult = self::getOld($this->arParams['ID'], $this->arParams['OFFSET']);
      $this->includeComponentTemplate();
    }
  }

  public function configureActions(): array
  {
    return [
      'create' => [
        'prefilters' => [
          new ActionFilter\Authentication(), // проверяет авторизован ли пользователь
        ]
      ],
      'get' => [
        'prefilters' => [
          new ActionFilter\Authentication(), // проверяет авторизован ли пользователь
        ]
      ]
    ];
  }

  function get($id, $offset)
  {

    /**
     * * массив коротких имен пользователей
     * @var array $users
     */
    $usersRes = \Bitrix\Main\UserTable::getList(array(
      'select' => array('SHORT_NAME', 'ID'),
    ))->fetchAll();
    foreach ($usersRes as $user) {
      $users[$user['ID']] = $user['SHORT_NAME'];
    }

    $hlbl = 4;
    $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    $commentsObj = $entity_data_class::getList(array(
      "select" => array("*"),
      "order" => array('ID' => "DESC"),
      "filter" => array('UF_UNIT' => $id),
      "offset" => $offset,
      'limit' => 10,
    ));
    $comments = [];
    while ($comment = $commentsObj->fetch()) {
      $date = $comment['UF_TIME']->format("Y.m.d в H:i");
      $text = $comment['UF_COMMENT'];
      $id = $comment['ID'];
      $user = $users[$comment['UF_USER']];
      $comments[] = array(
        "date" => $date,
        "text" => $text,
        "user" => $user,
        "id" => $id,
      );
    }
    return array_reverse($comments);
  }

  function create($unit, $text)
  {
    $hlbl = 4;
    $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    // Массив полей для добавления
    $xml_id = \Bitrix\Main\Security\Random::getString(10);
    $data = array(
      "UF_XML_ID" => $xml_id,
      "UF_UNIT" => $unit,
      "UF_USER" => \Bitrix\Main\Engine\CurrentUser::get()->getId(),
      "UF_COMMENT" => $text,
    );
    $result = $entity_data_class::add($data);

    $arrayComments = CIBlockElement::GetByID($unit)->GetNextElement()->GetProperties()['COMMENTS']['VALUE'];
    if (!is_array($arrayComments)) {
      $arrayComments = array($arrayComments);
    }
    $arrayComments = $arrayComments ? $arrayComments : array();
    $arrayComments = array_merge($arrayComments, array($xml_id));

    CIBlockElement::SetPropertyValuesEx($unit, false, array('COMMENTS' => $arrayComments));


    $new_comment = $entity_data_class::getList(array(
      "select" => array("*"),
      "filter" => array("UF_XML_ID" => $xml_id)
    ))->fetch();
    $date = $new_comment['UF_TIME']->format("Y.m.d в H:i");
    $text = $new_comment['UF_COMMENT'];
    $id = $new_comment['ID'];
    $user = \Bitrix\Main\UserTable::getList(array(
      'select' => array('SHORT_NAME'),
      'order' => array(),
      'filter' => array(
        '=ID' => $new_comment['UF_USER'],
      ),
    ))->fetch()['SHORT_NAME'];
    $response = array(
      "date" => $date,
      "text" => $text,
      "user" => $user,
      "id" => $id,
    );

    // pull
    self::notific([
      'id' => (int)$unit,
      'res' => $response,
      'arrayComments' => $arrayComments,
    ]);
    // pull

    return $response;
  }

  function notific($response)
  {
    $status = CPullOptions::GetNginxStatus();
    CPullStack::AddShared(array(
      'module_id' => 'addNewCommentNotification',
      'command' => 'sendComment',
      'params' => array(
        'status' => $status,
        'response' => $response,
      ),
    ));
  }

  public function createAction($unit, $text)
  {
    try {
      return [
        "result" => self::create($unit, $text),
      ];
    } catch (Exceptions\EmptyEmail $e) {
      $this->errorCollection[] = new Error($e->getMessage());
      return [
        "result" => "Произошла ошибка",
      ];
    }
  }

  public function getAction($unit, $offset)
  {
    try {
      return [
        "result" => self::get($unit, $offset),
      ];
    } catch (Exceptions\EmptyEmail $e) {
      $this->errorCollection[] = new Error($e->getMessage());
      return [
        "result" => "Произошла ошибка",
      ];
    }
  }
}
