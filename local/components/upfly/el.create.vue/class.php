<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Contract\Controllerable;
use CBitrixComponent;

class ElementCreate extends CBitrixComponent implements Controllerable, Errorable
{

   protected ErrorCollection $errorCollection;
   public $arParams;


   public function onPrepareComponentParams($arParams)
   {
      $this->errorCollection = new ErrorCollection();

      foreach ($arParams as $key => $param) {
         $arParams[$key] =  is_array($param) ? array_diff($param, array('')) : $param;
      }
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


   public function executeComponent()
   {
      CModule::IncludeModule('iblock');
      CModule::IncludeModule('highloadblock');

      $res = CIBlockElement::GetList(array(), array("IBLOCK_ID" => 1, "PROPERTY_PERSON_COMP" => \Bitrix\Main\Engine\CurrentUser::get()->getId()), false, false, array("ID"));
      while ($obj = $res->GetNext()) $companiesIds[] = $obj['ID'];
      //Проверка доступа
      $arFilter = array("IBLOCK_ID" => $this->arParams['IBLOCK_ID'], "ID" => $this->arParams['ELEMENT_ID']);
      $res = CIBlockElement::GetList(array(), $arFilter);
      if (!$companiesIds) {
         $companiesIds = array(0);
      }


      if ($this->startResultCache()) {
         // для получения параметров
         $this->arResult = [
            'COMPONENT_ID' => $this->componentId(), 'SCRIPT_PATH' => $_SERVER['SCRIPT_NAME']
         ];

         // * добавление имен на дефолтные поля
         $defaultOptions = array(
            'NAME' => array(
               'NAME' => 'Наименование',
               'TYPE' => 'STRING'
            ),
            'PREVIEW_TEXT' => array(
               'NAME' => 'Текст анонса',
               'TYPE' => 'HTML'
            ),
            'PREVIEW_PICTURE' => array(
               'NAME' => 'Картинка анонса',
               'TYPE' => 'FILE'
            ),
            'DETAIL_TEXT' => array(
               'NAME' => 'Текст детальной',
               'TYPE' => 'HTML'
            ),
            'DETAIL_PICTURE' => array(
               'NAME' => 'Картинка детальной',
               'TYPE' => 'FILE'
            ),
         );

         foreach ($this->arParams['OPTIONS'] as $code) {
            if ($code) {
               $prop = $defaultOptions[$code];
               if (in_array($code, $this->arParams['REQUIRED_FIELDS'])) {
                  $prop['REQUIRED'] = true;
               }
               $this->arResult['OPTIONS'][$code] = $prop;
            }
         }

         foreach ($this->arParams['FIELDS'] as $code) {
            if ($code) {
               $prop = CIBlockProperty::GetByID($code, $this->arParams['IBLOCK_ID'])->fetch();
               if (in_array($code, $this->arParams['REQUIRED_FIELDS'])) {
                  $prop['REQUIRED'] = true;
               }
               if (in_array($code, $this->arParams['LINK_ADD'])) {
                  $prop['LINK_ADD'] = true;
               }
               switch ($prop['PROPERTY_TYPE']) {
                  case 'S':
                     if (isset($prop['USER_TYPE_SETTINGS']['TABLE_NAME'])) {
                        # справочник
                        $this->arResult['FIELDS'][$code] = $prop;
                        if ($prop['MULTIPLE'] == 'Y') {
                           $this->arResult['FIELDS'][$code]['TYPE'] = 'CHECKBOX';
                        } else {
                           $this->arResult['FIELDS'][$code]['TYPE'] = 'SELECT';
                        }

                        $hlbl = Bitrix\Highloadblock\HighloadBlockTable::getList([
                           'filter' => ['=TABLE_NAME' => $prop['USER_TYPE_SETTINGS']['TABLE_NAME']]
                        ])->fetch()['ID'];
                        $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getById($hlbl)->fetch();
                        $entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
                        $entity_data_class = $entity->getDataClass();
                        $res = $entity_data_class::getList(array(
                           "select" => array("*"),
                        ));
                        while ($obj = $res->fetch()) {
                           $this->arResult['FIELDS'][$code]['VARIABLES'][$obj['UF_XML_ID']] = $obj['UF_NAME'];
                        }
                     } else  if ($prop['USER_TYPE'] == 'Date' || $prop['USER_TYPE'] == 'DateTime') {
                        # дата
                        $this->arResult['DATES'][$code] = $prop;
                        $this->arResult['DATES'][$code]['TYPE'] = 'DATE';
                     } else {
                        # строка
                        $this->arResult['FIELDS'][$code] = $prop;
                        $this->arResult['FIELDS'][$code]['TYPE'] = 'STRING';
                     }
                     break;
                  case 'E':
                     # привязка к элементу
                     $this->arResult['FIELDS'][$code] = $prop;
                     if ($prop['MULTIPLE'] == 'Y') {
                        $this->arResult['FIELDS'][$code]['TYPE'] = 'CHECKBOX';
                     } else {
                        $this->arResult['FIELDS'][$code]['TYPE'] = 'SELECT';
                     }

                     if($prop['LINK_IBLOCK_ID'] == 1){
                        $filter = array("IBLOCK_ID" => $prop['LINK_IBLOCK_ID'], "ID" => $companiesIds);
                     }else{
                        $filter = array("IBLOCK_ID" => $prop['LINK_IBLOCK_ID'], "PROPERTY_COMPANY" => $companiesIds);
                     }
                    
                     $res = CIBlockElement::GetList(array(), $filter, false, false, array("ID", "NAME"));
                     while ($obj = $res->GetNext()) {
                        $this->arResult['FIELDS'][$code]['VARIABLES'][$obj['ID']] = $obj['NAME'];
                     }
                     $this->arResult['FIELDS'][$code]['API_CODE'] = \Bitrix\Iblock\IblockTable::getByPrimary($prop['LINK_IBLOCK_ID'])->fetch()['API_CODE'];
                     break;
                  case 'N':
                     # число
                     $this->arResult['FIELDS'][$code] = $prop;
                     $this->arResult['FIELDS'][$code]['TYPE'] = 'INT';
                     break;
               }
            }
         }




         $this->arResult['delBtn'] = $this->arParams['DELETE_BTN'];
         $this->arResult['elementId'] = $this->arParams['ELEMENT_ID'];

         if ($this->arResult['elementId']) {
            $lstSelect = array_merge(array('IBLOCK_ID', 'ID'), $this->arParams['OPTIONS'], $this->arParams['FIELDS']);
            $dctFilter = [
               'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
               'ID' => $this->arResult['elementId'],
            ];
            $rdb = \CIBlockElement::GetList([], $dctFilter, false, false, $lstSelect)->getNextElement();
            $resOpt = $rdb->getFields();
            foreach ($this->arParams['OPTIONS'] as $key) {
               $options[$key] = $resOpt[$key];
            }
            $resFie = array_column($rdb->getProperties(), 'VALUE', 'CODE');
            foreach ($this->arParams['FIELDS'] as $key) {
               $fields[$key] = $resFie[$key];
            }
            $el = [
               'FIELDS' => array_diff($fields, array('')),
               'OPTIONS' => array_diff($options, array('')),
            ];
            // unset($el['OPTIONS']['ID']);
            // unset($el['OPTIONS']['IBLOCK_ID']);
            $this->arResult['ELEMENT'] = $el;
         }

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
         'edit' => [
            'prefilters' => [
               new ActionFilter\Authentication(), // проверяет авторизован ли пользователь
            ]
         ],
         'delete' => [
            'prefilters' => [
               new ActionFilter\Authentication(), // проверяет авторизован ли пользователь
            ]
         ],
      ];
   }


   // получение параметров
   function getArParams($componentId, $scriptCast)
   {
      if (($scriptPath = base64_decode($scriptCast))
         && ($arComponentData = self::componentData(
            $componentId,
            $scriptPath
         ))
      ) {
         /* полученный $arParams */
         return ($arComponentData['DATA']['PARAMS']);
      } else {
         return ('');
      }
   }
   protected function componentId()
   {
      $entryId = 'sometext';
      $m = null;
      /* вычленим только уникальную цифровую часть идентификатора */
      if (preg_match(
         '/^bx_(.*)_' . $entryId . '$/',
         $this->getEditAreaId($entryId),
         $m
      )) {
         return $m[1];
      }
   }
   protected static function pageComponents($scriptPath)
   {
      if (is_file($absPath = $_SERVER['DOCUMENT_ROOT'] . $scriptPath)) {
         $arCounter = [];
         $fileContent = file_get_contents($absPath);
         $arComponents = \PHPParser::ParseScript($fileContent);
         foreach ($arComponents as &$r) {
            $arCounter[$r['DATA']['COMPONENT_NAME']]++;
            /* делаем ID как в методе getEditAreaId */
            $r['ID'] = abs(crc32($r['DATA']['COMPONENT_NAME']
               . '_' . $arCounter[$r['DATA']['COMPONENT_NAME']]));
         }
         return $arComponents;
      }
      throw new \Exception('File [' . $scriptPath . '] not found');
   }
   protected static function componentData($componentId, $scriptPath)
   {
      if (
         $componentId
         && ($arComponents = self::pageComponents($scriptPath))
      ) {
         foreach ($arComponents as $arData)
            if ($componentId == $arData['ID'])
               return $arData;
      }
   }
   // получение параметров конец

   //* добавление
   public function create(array $options = [], array $fields = []): array
   {
      // из параметров
      $iblockId = $this->arParams['IBLOCK_ID'];
      $requiredProps = $this->arParams['REQUIRED_FIELDS'];

      $err = array();
      $allProps = array_merge($fields, $options);
      foreach ($requiredProps as $code) {
         if (!$allProps[$code] && $code != '') {
            $err[] = $code;
         }
      }
      // Если перешел с переадрисации
      if(!empty($this->arParams['BACK_ID'])){
         CModule::IncludeModule("iblock");
         $backElement = CIBlockElement::GetByID($this->arParams['BACK_ID'])->GetNext();
         // Если инфоблок типов
         if($iblockId == 12){
            $fields['IBLOCK'] = $backElement['IBLOCK_ID'];
         }
      }

      if (!count($err)) {
         CModule::IncludeModule("iblock");
         $el = new CIBlockElement;
         $arLoadProductArray = array_merge(
            array(
               "MODIFIED_BY"    => \Bitrix\Main\Engine\CurrentUser::get()->getId(),
               "IBLOCK_SECTION_ID" => false,
               "IBLOCK_ID"      => $iblockId,
               "PROPERTY_VALUES" => $fields,
               "ACTIVE"         => "Y",
            ),
            $options,
         );
         return (array(
            'SUCCESS' => $el->Add($arLoadProductArray),
            "BACK_URL" => $backElement['DETAIL_PAGE_URL']
         ));
      } else {
         return (array(
            'ERROR' => $err,
         ));
      }
   }

   public function createAction($componentId, $scriptCast, $back = "", array $options = [], array $fields = [],): array
   {
      try {
         // получение параметров
         $this->arParams = self::getArParams($componentId, $scriptCast);
         $this->arParams['BACK_ID'] = $back;
         $err = $this->create($options, $fields);
         return [
            "result" => $err,
         ];
      } catch (Exceptions\EmptyEmail $e) {
         $this->errorCollection[] = new Error($e->getMessage());
         return [
            "result" => $this->errorCollection,
         ];
      }
   }


   //* изменение
   public function edit(array $options = [], array $fields = [])
   {
      CModule::IncludeModule("iblock");

      $iblockId = $this->arParams['IBLOCK_ID'];
      $elementId = $this->arParams['ELEMENT_ID'];

      // добавить неизменяемые поля из админки
      if ($this->arParams['NO_EDITABLE_FIELDS']) {
         $noEditableProps = array_diff($this->arParams['NO_EDITABLE_FIELDS'], array(''));
         $arFilter = array("IBLOCK_ID" => $iblockId, "ID" => $elementId);
         $preProps = CIBlockElement::GetList(false, $arFilter, false, false, false)->GetNextElement()->GetProperties();
         foreach ($noEditableProps as $code) {
            if ($preProps[$code]['VALUE_ENUM_ID']) {
               // тип справочник или список
               $fields[$code] = array('VALUE' => $preProps[$code]['VALUE_ENUM_ID']);
            } else {
               $fields[$code] = $preProps[$code]['VALUE'];
            }
         }
      }
      // в целях безопасности

      // обязательные поля
      if ($this->arParams['REQUIRED_FIELDS']) {
         $requiredProps = array_diff($this->arParams['REQUIRED_FIELDS'], array(''));
         $err = array();
         $allProps = array_merge($fields, $options);
         foreach ($requiredProps as $code) {
            if (!$allProps[$code] && $code != '') {
               $err[] = $code;
            }
         }
      }

      if (!$err) {
         $el = new CIBlockElement;
         $arLoadProductArray = array_merge(
            array(
               "MODIFIED_BY"    => \Bitrix\Main\Engine\CurrentUser::get()->getId(),
               "IBLOCK_SECTION" => false,
               "PROPERTY_VALUES" => $fields,
            ),
            $options
         );
         return array(
            "SUCCESS" => $el->Update($this->arParams['ELEMENT_ID'], $arLoadProductArray),
         );
      } else {
         return array(
            "ERROR" => $err
         );
      }
   }
   public function editAction($componentId, $scriptCast, $elementId, array $options = [], array $fields = [],)
   {
      try {
         // получение параметров
         $this->arParams = self::getArParams($componentId, $scriptCast);
         $this->arParams['ELEMENT_ID'] = $elementId;
         $err = $this->edit($options, $fields);
         return [
            "result" => $err,
         ];
      } catch (Exceptions\EmptyEmail $e) {
         $this->errorCollection[] = new Error($e->getMessage());
         return [
            "result" => $this->errorCollection,
         ];
      }
   }

   //* удаление
   public function deleteAction($elementId)
   {
      try {
         CModule::IncludeModule("iblock");
         $err = CIBlockElement::Delete($elementId);
         return [
            "result" => $err,
         ];
      } catch (Exceptions\EmptyEmail $e) {
         $this->errorCollection[] = new Error($e->getMessage());
         return [
            "result" => $this->errorCollection,
         ];
      }
   }
}
