<?

use Bitrix\Main\Loader;

Loader::includeModule("highloadblock");
Loader::includeModule('iblock');

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

// проверка доступа
$res = CIBlockElement::GetList(array(), array("IBLOCK_ID" => 1, "PROPERTY_PERSON_COMP" => \Bitrix\Main\Engine\CurrentUser::get()->getId()), false, false, array("ID"));
while ($obj = $res->GetNext()) $companiesIds[] = $obj['ID'];
if (!$companiesIds) {
  $companiesIds = array(0);
}

// фильтры
// типы
// $filters['PROPERTY_TYPE']['NAME'] = 'Типы';
// $filters['PROPERTY_TYPE']['ARR'] = \Bitrix\Iblock\Elements\ElementTypesTable::getList(array(
//   'order' => array(), // сортировка
//   'select' => array('ID', 'NAME', 'COMPANY_' => 'COMPANY', 'IBLOCK_PROP_' => 'IBLOCK_PROP'), // выбираемые поля, без свойств. Свойства можно получать на старом ядре \CIBlockElement::getProperty
//   'filter' => array('IBLOCK_ID' => 12, 'COMPANY_VALUE' => $companiesIds), // фильтр только по полям элемента, свойства (PROPERTY) использовать нельзя
//   'data_doubling' => false, // разрешает получение нескольких одинаковых записей
//   'cache' => array( // Кеш запроса. Сброс можно сделать методом \Bitrix\Iblock\ElementTable::getEntity()->cleanCache();
//     'ttl' => 3600, // Время жизни кеша
//     'cache_joins' => true // Кешировать ли выборки с JOIN
//   ),
// ))->fetchAll();

// подразделения
$filters['PROPERTY_SUBDIVISION']['NAME'] = 'Подразделения';
$filters['PROPERTY_SUBDIVISION']['ARR'] = \Bitrix\Iblock\Elements\ElementDivisionsTable::getList(array(
  'order' => array(), // сортировка
  'select' => array('ID', 'NAME', 'COMPANY_' => 'COMPANY'), // выбираемые поля, без свойств. Свойства можно получать на старом ядре \CIBlockElement::getProperty
  'filter' => array('IBLOCK_ID' => 13, 'COMPANY_VALUE' => $companiesIds), // фильтр только по полям элемента, свойства (PROPERTY) использовать нельзя
  'data_doubling' => false, // разрешает получение нескольких одинаковых записей
  'cache' => array( // Кеш запроса. Сброс можно сделать методом \Bitrix\Iblock\ElementTable::getEntity()->cleanCache();
    'ttl' => 3600, // Время жизни кеша
    'cache_joins' => true // Кешировать ли выборки с JOIN
  ),
))->fetchAll();

// комментарии
$filters['PROPERTY_COMMENTS']['NAME'] = GetMessage('COMMENTS');
$filters['PROPERTY_COMMENTS']['ARR'] = array(
  array(
    'UF_XML_ID' => '%%',
    "UF_NAME" => GetMessage('YES_COMMENTS')
  ),
  array(
    'UF_XML_ID' => 'false',
    "UF_NAME" => GetMessage('NO_COMMENTS')
  )
);


$arResult['FILTERS'] = $filters;

// фильтр по компаниям
$companies = \Bitrix\Iblock\ElementTable::getList(array(
  'order' => array(), // сортировка
  'select' => array('ID', 'NAME'), // выбираемые поля, без свойств. Свойства можно получать на старом ядре \CIBlockElement::getProperty
  'filter' => array('IBLOCK_ID' => 1, "=ID" => $companiesIds), // фильтр только по полям элемента, свойства (PROPERTY) использовать нельзя
  'data_doubling' => false, // разрешает получение нескольких одинаковых записей
  'cache' => array( // Кеш запроса. Сброс можно сделать методом \Bitrix\Iblock\ElementTable::getEntity()->cleanCache();
    'ttl' => 3600, // Время жизни кеша
    'cache_joins' => true // Кешировать ли выборки с JOIN
  ),
))->fetchAll();
$arResult['COMPANIES'] = $companies;


// иконка комментариев
// foreach ($arResult["ITEMS"] as $key => $value) {
//   $hlbl = 4;
//   $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();
//   $entity = HL\HighloadBlockTable::compileEntity($hlblock);
//   $entity_data_class = $entity->getDataClass();
//   $rsData = $entity_data_class::getList(array(
//     "select" => array("UF_UNIT"),
//     "order" => array(),
//     "limit" => 1,
//     "filter" => array("UF_UNIT" => $value['ID'])
//   ))->fetchAll();
//   if ($rsData) {
//     $status = true;
//   } else {
//     $status = false;
//   }
//   $arResult["ITEMS"][$key]['COMMENTS_STATUS'] = $status;
// }

// название раздела
$iblock_res = CIBlock::GetList(array(), array(), false);
while ($value = $iblock_res->fetch()) {
  $arResult['IBLOCK_NAME'][$value['ID']] = $value['NAME'];
};

// массив для вывода в таблице
// foreach ($filters['PROPERTY_TYPE']['ARR'] as $value) {
//   $arResult['TYPE_NAME'][$value['ID']] = $value['NAME'];
// }
// детальный урл
foreach ($arResult["ITEMS"] as $key => $arItem) {
  $res = CIBlockElement::GetList(array(), array('ID' => $arItem['ID']), false, false, array('DETAIL_PAGE_URL'));
  $urlMask = $res->fetch()['DETAIL_PAGE_URL'];
  $url = str_replace('#ELEMENT_ID#', $arItem['ID'], str_replace('#SITE_DIR#', '', $urlMask));
  $arResult["ITEMS"][$key]['DETAIL_PAGE_URL'] = $url;
}
