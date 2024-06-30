<?
// Класс реализует экспорт данных в фид для яндекс врачей
class FeedUpfly
{

	static private $pathToFeed = '/upload/custom_feeds/';
	// static private $IblockIdPersonal = ;

	// Инициализация библиотек
	static protected function init()
	{

		\Bitrix\Main\Loader::includeModule("iblock");
	}

	// Получение списка настроек
	static protected function getSettings()
	{
		$resultQuery = \Bitrix\Iblock\Elements\ElementFeedsTable::getList([
			'select' => ['ID', 'ACTIVE', 'NAME', 'CODE', 'SHOP_NAME_' => 'SHOP_NAME', 'COMPANY_NAME_' => 'COMPANY_NAME', 'SHOP_EMAIL_' => 'SHOP_EMAIL', 'CITY_' => 'CITY', 'DIRECTION_' => 'DIRECTION'],
			'filter' => ['=ACTIVE' => 'Y'],
		])->fetchAll();

		foreach ($resultQuery as $item) {
			if (!array_key_exists($item['ID'], $result)) {
				$result[$item['ID']] = [
					'ID' =>  $item['ID'],
					'NAME' =>  $item['NAME'],
					'CODE' =>  $item['CODE'],
					'PROPERTY_SHOP_NAME' =>  $item['SHOP_NAME_VALUE'],
					'PROPERTY_COMPANY_NAME' =>  $item['COMPANY_NAME_VALUE'],
					'PROPERTY_SHOP_EMAIL' =>  $item['SHOP_EMAIL_VALUE'],
					'PROPERTY_CITY' =>  (int) $item['CITY_VALUE'],
				];
			}

			$result[$item['ID']]['PROPERTY_DIRECTION'][] = $item['DIRECTION_VALUE'];
		}

		return $result;
	}
	// Получение списка городов
	static protected function getCities()
	{
		$resultQuery = \Bitrix\Iblock\Elements\ElementRegionsTable::getList([
			'select' => ['ID', 'ACTIVE', 'NAME', 'ADDRESS_' => 'ADDRESS', 'ENTITY_' => 'ENTITY', 'MAIN_DOMAIN_' => 'MAIN_DOMAIN', 'CLINIC_NAME_' => 'CLINIC_NAME'],
			'filter' => ['=ACTIVE' => 'Y'],
		])->fetchAll();

		foreach ($resultQuery as $item) {
			$result[$item['ID']] = $item;
			$result[$item['ID']]['ADDRESS_VALUE'] = trim(unserialize($result[$item['ID']]['ADDRESS_VALUE'])['TEXT']);
		}

		return $result;
	}
	// Получение отзывов
	static protected function getReviews()
	{
		$resultQuery = \Bitrix\Iblock\Elements\ElementReviewsTable::getList([
			'select' => ['ID', 'ACTIVE', 'NAME', 'LINK_STAFF_' => 'LINK_STAFF', 'RATING_' => 'RATING'],
			'filter' => ['=ACTIVE' => 'Y', '!LINK_STAFF_VALUE' => false],
		])->fetchAll();

		$rating_list = [
			138 => 1,
			139 => 2,
			140 => 3,
			141 => 4,
			142 => 5,
		];

		foreach ($resultQuery as $item) {
			$result[$item['ID']] = [
				'ID' =>  $item['ID'],
				'PROPERTY_RATING' =>  $rating_list[$item['RATING_VALUE']],
				'PROPERTY_LINK_STAFF' =>  (int) $item['LINK_STAFF_VALUE'],
			];
		}

		return self::calculateReviews($result);
	}
	// Подсчёт отзывов
	static private function calculateReviews($reviews)
	{
		foreach ($reviews as $item) {
			$result[$item['PROPERTY_LINK_STAFF']]['COUNT'] = ++$result[$item['PROPERTY_LINK_STAFF']]['COUNT'];
			$result[$item['PROPERTY_LINK_STAFF']]['RATING'] += $item['PROPERTY_RATING'];
		}
		return  $result;
	}
	// Получение списка специалистов
	static protected function getPersonal($reviews)
	{
		$resultQuery = \Bitrix\Iblock\Elements\ElementPersonalTable::getList([
			'select' => ['ID', 'CODE', 'SECTIONS_' => 'SECTIONS', 'ACTIVE', 'NAME', 'PREVIEW_PICTURE', 'LINK_REGION_' => 'LINK_REGION', 'CHECK_FEED_' => 'CHECK_FEED', 'PRICE_FEED_' => 'PRICE_FEED', 'SPECIALIZATION_' => 'SPECIALIZATION', 'WORK_' => 'WORK', 'EDUCATION_' => 'EDUCATION', 'QUALIFICATION_' => 'QUALIFICATION'],
			'filter' => ['=ACTIVE' => 'Y', '=CHECK_FEED_VALUE' => 333],
		])->fetchAll();

		foreach ($resultQuery as $item) {
			if (!array_key_exists($item['ID'], $result)) {
				$result[$item['ID']] = [
					'ID' =>  $item['ID'],
					'NAME' =>  $item['NAME'],
					'URL' =>  '/company/staff/' . $item['SECTIONS_CODE'] . '/' . $item['CODE'] . '/',
					'PRICE' =>  $item['PRICE_FEED_VALUE'],
					'PROPERTY_CITY' =>  (int) $item['LINK_REGION_VALUE'],
					'PICTURE' =>  !empty($item['PREVIEW_PICTURE']) ? CFile::GetPath($item['PREVIEW_PICTURE']) : '/upload/default_personal.png',
					'PROPERTY_WORK' =>  preg_replace('/[^0-9]/', "", $item['WORK_VALUE']),
					'RATING' =>  $reviews[$item['ID']]['RATING'] ? $reviews[$item['ID']]['RATING'] / $reviews[$item['ID']]['COUNT'] : 0,
					'COUNT_REVIEW' =>  $reviews[$item['ID']]['COUNT'],
					'PROPERTY_EDUCATION' => trim(strip_tags(unserialize($item['EDUCATION_VALUE'])['TEXT'])),
					'PROPERTY_QUALIFICATION' =>  $item['QUALIFICATION_VALUE'],
				];
			}
			$result[$item['ID']]['SECTION_CODE'][] = $item['SECTIONS_CODE'];
			$result[$item['ID']]['SECTION_ID'][] = $item['SECTIONS_ID'];
			$result[$item['ID']]['SECTION_NAME'][] = $item['SECTIONS_NAME'];
			// $result[$item['ID']]['PROPERTY_SPECIALIZATION'][] = $item['SPECIALIZATION_VALUE'];
			// $result[$item['ID']]['PROPERTY_CITY'][] = (int) $item['LINK_REGION_VALUE'];
		}

		return $result;
	}
	// Создание строки
	static protected function writeString($settings, $cities, $personal)
	{
		foreach ($settings as $value) {
			$sets = $offers = '';
			$set_ids = '<set-ids>';
			foreach ($personal as $spec) {
				// если регион врача и направление соответствует настройке
				if ($spec['PROPERTY_CITY'] == $value['PROPERTY_CITY']) {
					// сеты
					foreach ($spec['SECTION_ID'] as $key => $el) {
						if (in_array($el, $value['PROPERTY_DIRECTION'])) {
							$sets .= '<set id="' . $spec['SECTION_CODE'][$key] . '">
                                <name>' . $spec['SECTION_NAME'][$key] . '</name>
                                <url>https://' . $cities[$spec['PROPERTY_CITY']]['MAIN_DOMAIN_VALUE'] . '/company/staff/' . $spec['SECTION_CODE'][$key] . '/</url>
                            </set>';
							$set_ids .= $spec['SECTION_CODE'][$key] . ',';
						}
					}
					if (empty($sets)) continue;
					$set_ids = substr($set_ids, 0, -1) . '</set-ids>';
					// офферы
					$arr_names = explode(' ', $spec['NAME']);
					$offers = '<offer id="vrach' . $spec['ID'] . '" group_id="' . $spec['ID'] . '">
                                <name>' . $spec['NAME'] . '</name>
                                <url>https://' . $cities[$spec['PROPERTY_CITY']]['MAIN_DOMAIN_VALUE'] . $spec['URL'] . '</url>
                                <price from="true">' . $spec['PRICE'] . '</price>
                                <currencyId>RUR</currencyId>
                                ' . $set_ids . '
                                <picture>https://' . $cities[$spec['PROPERTY_CITY']]['MAIN_DOMAIN_VALUE'] . $spec['PICTURE'] . '</picture>
                                <categoryId>1</categoryId>
                                <param name="Фамилия">' . $arr_names[0] . '</param>
                                <param name="Имя">' . $arr_names[1] . '</param>
                                <param name="Отчество">' . $arr_names[2] . '</param>
                                <param name="Годы опыта">' . $spec['PROPERTY_WORK'] . '</param>
                                <param name="Город">г. ' . $cities[$spec['PROPERTY_CITY']]['NAME'] . '</param>
                                ' . (!empty($spec['RATING']) && $spec['RATING'] != 0 ? '<param name="Средняя оценка">' . $spec['RATING'] . '</param>' : '') . '
                                ' . (!empty($spec['COUNT_REVIEW']) && $spec['COUNT_REVIEW'] != 0 ? '<param name="Число отзывов">' . $spec['COUNT_REVIEW'] . '</param>' : '') . '
                                ' . (!empty($value['PROPERTY_EDUCATION']) ? '<param name="Степень">' . $value['PROPERTY_EDUCATION'] . '</param>' : '') . '
                                ' . (!empty($value['PROPERTY_QUALIFICATION']) ? '<param name="Категория">' . $value['PROPERTY_QUALIFICATION'] . '</param>' : '') . '
                                <param name="Город клиники">г. ' . $cities[$spec['PROPERTY_CITY']]['NAME'] . '</param>
                                <param name="Адрес клиники">г. ' . $cities[$spec['PROPERTY_CITY']]['ADDRESS_VALUE'] . '</param>
                                <param name="Название клиники">' . $cities[$spec['PROPERTY_CITY']]['CLINIC_NAME_VALUE'] . '</param>
                                <param name="Возможность записи">true</param>
                                </offer>';
				}
			}
			// общий шаблон
			$str = '<yml_catalog date="' . date('Y-m-d H:i') . '">
                <shop>
                    <name>' . $value['PROPERTY_SHOP_NAME'] . '</name>
                    <company>' . $value['PROPERTY_COMPANY_NAME'] . '</company>
                    <url>https://' . $cities[$value['PROPERTY_CITY']]['MAIN_DOMAIN_VALUE'] . '/</url>
                    ' . (!empty($value['PROPERTY_SHOP_EMAIL']) ? '<email>' . $value['PROPERTY_SHOP_EMAIL'] . '</email>' : '') . '
                    <picture>https://' . $cities[$value['PROPERTY_CITY']]['MAIN_DOMAIN_VALUE'] . '/upload/logo.png</picture>
                    <currencies>
                        <currency id="RUR" rate="1"/>
                    </currencies>
                    <categories>
                        <category id="1">Врач</category>
                    </categories>
                    <sets>' . $sets . '</sets>
                    <offers>' . $offers . '</offers>
                </shop>
            </yml_catalog>';
			// записываем в файл
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . self::$pathToFeed . $value['CODE'] . '.xml', $str);
		}
	}
	// Формирование данных
	static public function CreateFeed()
	{
		self::init();

		$settings = self::getSettings();
		$cities = self::getCities();
		$reviews = self::getReviews();
		$personal = self::getPersonal($reviews);

		self::writeString($settings, $cities, $personal);
	}

	// Формирование данных через агента
	// static public function CreateFeedAgent()
	// {
	//     self::CreateFeed();

	//     return 'FeedUpfly::CreateFeedAgent();';
	// }
}
