<? CModule::IncludeModule("iblock");

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

$iblockIds = $arParams['IBLOCK_IDS'];
global $propsCodes;
$fieldsCodes = $arParams['FIELDS'];
$propsCodes = $arParams['PROPS'];
// права доступа
$res = CIBlockElement::GetList(array(), array("IBLOCK_ID" => 1, "PROPERTY_PERSON_COMP" => \Bitrix\Main\Engine\CurrentUser::get()->getId()), false, false, array("ID"));
while ($obj = $res->GetNext()) $companiesIds[] = $obj['ID'];
if (!$companiesIds) {
	$companiesIds = array(0);
}
// фильтр по компаниям
global $filterComp;
if ($request->get('filter_company')) {
	$filterComp = explode('_', $request->get('filter_company'));
} else {
	$filterComp = array();
}
$companiesIdsFilter = array_filter($companiesIds, function ($e) {
	global $filterComp;
	if (in_array($e, $filterComp)) {
		return $e;
	}
});
if (reset($companiesIdsFilter)) {
	$companiesIds = $companiesIdsFilter;
}
// фильтр по дате
global $filterDate;
if ($request->get('filter_date')) {
	$filterDate = $request->get('filter_date');
} else {
	$filterDate = date("01.m.Y");
}

// пагинация
$nav = new \Bitrix\Main\UI\AdminPageNavigation("PAGEN_1");
$nav->allowAllRecords(true)
	->setPageSize($request->get('count') ? $request->get('count') : $arParams['NEWS_START_COUNT'])
	->initFromUri();

$entityIblocks = [];
$propsResNames = [];
$propsCompaniesNames = [];
$runtimeCompanies = [];

$filter = Bitrix\Main\ORM\Query\Query::filter();
foreach ($iblockIds as $id) {
	$iblock = \Bitrix\Iblock\IblockTable::getList(array(
		"filter" => array(
			'ID' => $id
		),
		'select' => array(
			'CODE'
		)
	))->fetch();
	$props = \Bitrix\Iblock\PropertyTable::getList(array(
		"filter" => array(
			'IBLOCK_ID' => $id
		),
	))->fetchAll();

	$propsNames = [];

	foreach ($props as $e) {
		if (in_array($e['CODE'], $propsCodes)) {
			$propsNames['PROPERTY_' . $e['ID']] = ['data_type' => 'string'];
			$propsResNames[$e['CODE'] . '_' . $id] = 'PROP_EL_' . strtoupper($iblock['CODE']) . '.PROPERTY_' . $e['ID'];
			// фильтрация по компаниям
			if ($e['CODE'] == 'COMPANY') {
				$propsCompaniesNames[] = array(
					"PROP_EL_" . strtoupper($iblock['CODE']) . ".PROPERTY_" . $e['ID'],
					'in',
					$companiesIds
				);
			}
			// фильтрация по датам
			if ($e['CODE'] == 'PRICE_TO_MONTH' && $iblock['CODE'] == 'services') {
				$propsDatesNames[] = array(
					"PROP_EL_" . strtoupper($iblock['CODE']) . ".PROPERTY_" . $e['ID'],
					'like',
					'%' . date('m.Y', strtotime($filterDate)) . '%'
				);
			}
			if ($e['CODE'] == 'DATE' && $iblock['CODE'] == 'planning') {
				$propsDatesNames[] = array(
					'logic' => 'and',
					array(
						"PROP_EL_" . strtoupper($iblock['CODE']) . ".PROPERTY_" . $e['ID'],
						">=",
						date('Y-m-d 00:00:00', strtotime($filterDate))
					),
					array(
						"PROP_EL_" . strtoupper($iblock['CODE']) . ".PROPERTY_" . $e['ID'],
						"<=",
						date('Y-m-d 23:59:59', strtotime('-1 day', strtotime('+1 month', strtotime($filterDate)))),
					)
				);
			}
			if ($e['CODE'] == 'END_DATE' && $iblock['CODE'] == 'software') {
				$propsDatesNames[] = array(
					'logic' => 'and',
					array(
						"PROP_EL_" . strtoupper($iblock['CODE']) . ".PROPERTY_" . $e['ID'],
						">=",
						date('Y-m-d 00:00:00', strtotime($filterDate))
					),
					array(
						"PROP_EL_" . strtoupper($iblock['CODE']) . ".PROPERTY_" . $e['ID'],
						"<=",
						date('Y-m-d 23:59:59', strtotime('-1 day', strtotime('+1 month', strtotime($filterDate)))),
					)
				);
			}
		}
	}

	$entityIblocks[$id] = \Bitrix\Main\Entity\Base::compileEntity(
		'Props' . $iblock['CODE'],
		array_merge(
			[
				'IBLOCK_ELEMENT_ID' => ['data_type' => 'string'],
				'NAME' => ['data_type' => 'string'],
			],
			$propsNames
		),
		[
			'table_name' => 'b_iblock_element_prop_s' . $id,
		]
	);
	$runtimeCompanies[] = new Bitrix\Main\ORM\Fields\Relations\Reference(
		'PROP_EL_' . strtoupper($iblock['CODE']),
		$entityIblocks[$id],
		[
			'=ref.IBLOCK_ELEMENT_ID' => 'this.ID',
		]
	);
}
// фильтрация по ифоблокам
$filter->where('IBLOCK_ID', 'in', $iblockIds);
// фильтрация по компаниям
$filter->where(
	$filter->createFromArray(array_merge(
		["logic" => "or"],
		$propsCompaniesNames
	)),
);
// фильтрация по дате
$filter->where(
	$filter->createFromArray(array_merge(
		["logic" => "or"],
		$propsDatesNames
	))
);
// фильтр по отмеченным
if ((bool)$request->get('selected') == true) {
	$filter->where('ID', 'in', explode('_', trim($request->get('selected'))));
};

// сортировка
if ($request->get('sort') && $request->get('order')) {
	$sort = array(
		$request->get('sort') => $request->get('order')
	);
} else {
	$sort = array();
}



// $filter->where('PROP_EL_SERVICES.PROPERTY_73', 'w');
$Query = \Bitrix\Iblock\ElementTable::getList([
	'filter' => $filter,
	"offset" => $nav->getOffset(),
	"limit" => $nav->getLimit(),
	"count_total" => true,
	'runtime' => $runtimeCompanies,
	'select' => array_merge($fieldsCodes, $propsResNames),
	'order' => $sort
]);
$resultQuery = $Query->FetchAll();
foreach ($resultQuery as $key => $el) {
	foreach ($el as $k => $value) {
		$resultQuery[$key][str_replace(('_' . $el['IBLOCK_ID']), '', $k)] = $value; //
		if (stripos($k, ('_' . $el['IBLOCK_ID']))) {
			unset($resultQuery[$key][$k]);
		} else if ($value == NULL) {
			unset($resultQuery[$key][$k]);
		}
	}
}
// услуги прайс
foreach ($resultQuery as $key => $arItem) {
	if ($arItem['IBLOCK_ID'] == 4) {
		$res = CIBlockElement::GetList(array(), array('ID' => $arItem['ID']), false, false, array('PROPERTY_SINGLE_SUMM', 'PROPERTY_PRICE_TO_MONTH'))->fetch();
		$prices = array();
		$mnozh = 0;
		$price = 0;
		foreach (unserialize($res['PROPERTY_PRICE_TO_MONTH_VALUE']) as $value) {
			$prices = array_merge($value, $prices);
		}
		$mnozh = $prices[str_replace('01.', '', $filterDate)];
		$price = $res['PROPERTY_SINGLE_SUMM_VALUE'] * $mnozh;
		$resultQuery[$key]['PRICE'] = $price;
	}
}
$arResult['ITEMS'] = $resultQuery;

// вся сумма
$allSumm = 0;
$QueryAll = \Bitrix\Iblock\ElementTable::getList([
	'filter' => $filter,
	'runtime' => $runtimeCompanies,
	'select' => array_merge($fieldsCodes, $propsResNames),
]);
$resultQueryAll = $QueryAll->FetchAll();
foreach ($resultQueryAll as $key => $el) {
	foreach ($el as $k => $value) {
		$resultQueryAll[$key][str_replace(('_' . $el['IBLOCK_ID']), '', $k)] = $value; //
		if (stripos($k, ('_' . $el['IBLOCK_ID']))) {
			unset($resultQueryAll[$key][$k]);
		} else if ($value == NULL) {
			unset($resultQueryAll[$key][$k]);
		}
	}
}
foreach ($resultQueryAll as $key => $arItem) {
	if ($arItem['IBLOCK_ID'] == 4) {
		$res = CIBlockElement::GetList(array(), array('ID' => $arItem['ID']), false, false, array('PROPERTY_SINGLE_SUMM', 'PROPERTY_PRICE_TO_MONTH'))->fetch();
		$prices = array();
		$mnozh = 0;
		$price = 0;
		foreach (unserialize($res['PROPERTY_PRICE_TO_MONTH_VALUE']) as $value) {
			$prices = array_merge($value, $prices);
		}
		$mnozh = $prices[str_replace('01.', '', $filterDate)];
		$price = $res['PROPERTY_SINGLE_SUMM_VALUE'] * $mnozh;
		$resultQueryAll[$key]['PRICE'] = $price;
	}
	$allSumm += $resultQueryAll[$key]['PRICE'];
}
$arResult['ALL_SUMM'] = $allSumm;

// пагинация
$nav->setRecordCount($Query->getCount());
$arResult['NAV_OBJ'] = $nav;
$this->includeComponentTemplate();
