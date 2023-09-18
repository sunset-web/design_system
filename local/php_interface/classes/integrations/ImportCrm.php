<?
// Класс реализует импорт каталога с CRM
class ImportCrm
{
   function __construct()
   {
      CModule::IncludeModule("iblock");

      $this->CIBlockSection  = new \CIBlockSection;
      $this->CIBlockElement  = new \CIBlockElement;
      $this->CIBlockProperty = new \CIBlockProperty;
      $this->CIBlock = new \CIBlock;

      $this->domain = 'domain';
      $this->client_id = 'client_id';
      $this->client_secret = 'client_secret';

      $this->iblockId = 46;
      $this->downloadLink = 'downloadlink';
   }

   private function getLocation($headers)
   {
      if (preg_match('#Location: (.*)#', $headers, $matches))
         return trim($matches[1]);

      return '';
   }

   private function getCode($login, $pass)
   {
      $response = $this->request_auth('https://' . $this->domain . '/oauth/authorize/', [
         'response_type'   => 'сode',
         'client_id'       =>  $this->client_id,
         'AUTH_FORM'       => 'Y',
         'TYPE'            => 'AUTH',
         'backurl'         => '',
         'USER_LOGIN'      => $login,
         'USER_PASSWORD'   => $pass,
         'USER_REMEMBER'   => 'Y'
      ]);

      $url = $this->getLocation($response);

      $parseUrl = parse_url($url, PHP_URL_QUERY);
      parse_str($parseUrl, $queryArray);

      return $queryArray['code'] ? $queryArray['code'] : '';
   }

   private function request_auth($url, $postFields = [], $get_headers = true)
   {
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_HEADER, $get_headers);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_URL, $url);

      if (!empty($postFields)) {
         $queryString = http_build_query($postFields);

         curl_setopt($ch, CURLOPT_POST, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
      }

      $res = curl_exec($ch);
      curl_close($ch);

      return $res;
   }

   // get token
   protected function getToken($login, $pass)
   {
      $code = $this->getCode($login, $pass);

      $query = http_build_query([
         'grant_type'      => 'authorization_code',
         'expires'         => '1900000',
         'client_id'       => $this->client_id,
         'client_secret'   => $this->client_secret,
         'code'            => $code,
         'scope'           => 'crm'
      ]);

      $response = $this->request_auth('https://oauth.bitrix.info/oauth/token/?' . $query, [], false);

      return json_decode($response);
   }

   // request in bx24
   protected function request($method)
   {

      $result = array();

      for ($i = 1;; $i = ($i + \CRest::BATCH_COUNT)) {
         $resultRequest = \CRest::call($method, ['start' => $i]);
         $result = array_merge($result, $resultRequest['result']);

         if (!$resultRequest['next'])
            break;
      }

      return $result;
   }

   // download images
   protected function loadFromB24(string $pictureUrl, string $token)
   {

      // parse_str(parse_url($pictureUrl, PHP_URL_QUERY), $queryArray);

      // $queryArray['auth'] = $token;

      $detail_picture = \CFile::MakeFileArray($this->downloadLink . $pictureUrl . '&auth=' . $token);
      if (!$detail_picture) return false;
      $ext = explode('/', $detail_picture['type'])[1];

      $detail_picture['name'] = "detail." . $ext;

      $imgg = \CFile::ResizeImageGet(\CFile::SaveFile($detail_picture, "imgb24"), array('width' => 1500, 'height' => 1500), BX_RESIZE_IMAGE_PROPORTIONAL);

      $detail_picture_end = \CFile::MakeFileArray($imgg['src']);

      return $detail_picture_end;
   }

   //import props
   public function importProps()
   {

      $properties = self::request('crm.product.property.list');

      foreach ($properties as $property) {
         $arFields = [
            'ACTIVE'        => 'Y',
            'IBLOCK_ID'     => $this->iblockId,
            'NAME'          => $property['NAME'],
            'SORT'          => $property['SORT'],
            'CODE'          => $property['XML_ID'],
            'MULTIPLE'      => $property['MULTIPLE'],
            'PROPERTY_TYPE' => $property['PROPERTY_TYPE'],
            'VALUES'        => $property['VALUES']
         ];

         $searchProperty = $this->CIBlockProperty->GetList(
            [],
            [
               "CODE" => $property['XML_ID'],
               "IBLOCK_ID" => $this->iblockId
            ]
         )->Fetch();

         if (!$searchProperty) {
            $id = $this->CIBlockProperty->Add($arFields);
         }
      }
   }

   //import sections
   public function importSections()
   {

      $sections = self::request('crm.productsection.list');

      $parentSectionId = [];

      foreach ($sections as $section) {

         $sectionXmlId = "b24-section-" . $section['ID'];

         $arFields = [
            // "ACTIVE"    => 'Y',
            "IBLOCK_ID" => $this->iblockId,
            "NAME"      => $section['NAME'],
            "CODE"      => $section['CODE'],
            "XML_ID"    => $sectionXmlId
         ];

         if (array_key_exists('b24-section-' . $section['SECTION_ID'], $parentSectionId)) {
            $arFields["IBLOCK_SECTION_ID"] = $parentSectionId['b24-section-' . $section['SECTION_ID']];
         }

         $section_current = $this->CIBlockSection->GetList([], ['XML_ID' => $sectionXmlId], false, ["ID"])->Fetch();

         $id = $section_current['ID'];

         if ($id > 0) {
            $this->CIBlockSection->Update($id, $arFields);
         } else {
            $arFields["ACTIVE"] = "N";
            $id = $this->CIBlockSection->Add($arFields);
         }

         $parentSectionId[$sectionXmlId] = $id;
      }
   }

   //import elements
   public function importElements()
   {

      $elements = array();

      $properties = self::request('crm.product.property.list');

      $token = self::getToken('login', 'pass');

      $ids = array_column($properties, 'ID');

      array_walk($ids, function (&$item1, $key) {
         $item1 = 'PROPERTY_' . $item1;
      });

      for ($i = 1;; $i = ($i + \CRest::BATCH_COUNT)) {
         $resultRequest = \CRest::call(
            'crm.product.list',
            [
               'order'  => ["SORT" => "ASC"],
               // 'filter'  => ["ID" => "9766"], //её нет, берет весь результат
               'filter'  => ["SECTION_ID" => ["992", "994", "996", "998", "1000", "1002", "1004", "1006", "1008", "1010", "1012", "1014", "1016", "1018", "1020", "1022", "1024", "1026"]], //нет фильтрации по подразделам, выбираем конечный раздел - этаж
               'select' => array_merge(['ID', 'NAME', 'PRICE', 'SECTION_ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'], $ids),
               'start' => $i
            ]
         );

         $elements = array_merge($elements, $resultRequest['result']);

         if (!$resultRequest['next']) {
            //   CEventLog::Add(array(
            //   "AUDIT_TYPE_ID" => "REQ_BREAK16",
            //    "DESCRIPTION" => $i,
            // ));
            break;
         }
      }

      foreach ($elements as $appartment) {
         $propsWithValues = [];

         foreach ($properties as $property) {

            $propId = 'PROPERTY_' . $property['ID'];

            //list type
            if ($property['PROPERTY_TYPE'] == 'L') {
               $value = $property['VALUES'][$appartment[$propId]['value']]['VALUE'];

               if ($value || $value === '0') {
                  $propertyValue = \CIBlockPropertyEnum::GetList([], ["CODE" => $property['CODE'], 'VALUE' => $value])->Fetch();

                  $propsWithValues[$property['CODE']] = $propertyValue['ID'];
               } else {
                  $propsWithValues[$property['CODE']] = null;
               }
            }
            //file type
            else if ($property['PROPERTY_TYPE'] == 'F') {
               $propsWithValues[$property['CODE']] = null;

               if ($appartment[$propId]) {
                  $arLocalFiles = [];
                  $files = $appartment[$propId];

                  foreach ($files as $file) {
                     $arLocalFiles[] = self::loadFromB24($file['value']['downloadUrl'], $token->access_token);
                     sleep(0.4);
                  }

                  $propsWithValues[$property['CODE']] = $arLocalFiles;
               }
            }
            //others types
            else {
               $propsWithValues[$property['CODE']] = $appartment[$propId]['value'];
            }
         }


         $detail_picture = null;

         if ($appartment['PREVIEW_PICTURE'] || $appartment['DETAIL_PICTURE'] || $appartment['PROPERTY_320'][0]) {
            $pictureUrl = $appartment['DETAIL_PICTURE']['downloadUrl'];

            if (!$pictureUrl) {
               $pictureUrl = $appartment['PREVIEW_PICTURE']['downloadUrl'];
            }

            if (!$pictureUrl) {
               $pictureUrl = $appartment['PROPERTY_320'][0]['value']['downloadUrl'];
            }


            // $detail_picture = self::loadFromB24($pictureUrl, $token->access_token);

            // sleep(0.2);
         }

         $propsWithValues['CHESS_PRICE'] = $appartment['PRICE'];

         //    CEventLog::Add(array(
         //   "AUDIT_TYPE_ID" => "SINGLE_LOAD16",
         //    "DESCRIPTION" => $appartment['ID'].'__'.$appartment['NAME'],
         // ));
         $prepared[] = [
            'ID'              => $appartment['ID'],
            'SECTION_ID'      => $appartment['SECTION_ID'],
            'NAME'            => $appartment['NAME'],
            'DETAIL_PICTURE'  => $detail_picture,
            'PRICE'           => $appartment['PRICE'],
            'PROPERTIES'      => $propsWithValues
         ];
      }


      foreach ($prepared as $appartment) {


         $sectionsXmlId = "b24-section-" . $appartment['SECTION_ID'];
         $elementXmlId  = "b24-element-" . $appartment['ID'];

         $section = $this->CIBlockSection->GetList([], ['XML_ID' => $sectionsXmlId], false, ["ID"])->Fetch();
         $element = $this->CIBlockElement->GetList([], ['XML_ID' => $elementXmlId], false, ["ID"])->Fetch();

         $arElementProps = [];

         foreach ($appartment['PROPERTIES'] as $code => $property) {
            if (
               $code == 'CHESS_PRICE' ||
               $code == 'CHESS_TOTAL_REDUCED_AREA' ||
               $code == 'CHESS_FLOOR'
            ) {

               if ($code == 'CHESS_PRICE' || $code == 'CHESS_TOTAL_REDUCED_AREA') {
                  $statusValueId = $appartment['PROPERTIES']['CHESS_STATUS'];
                  $arPropValue = \CIBlockPropertyEnum::GetByID($statusValueId);

                  if ($arPropValue['XML_ID'] == 'free') {
                     if ($appartment['PROPERTIES']['CHESS_CITYBOX'] !== null) {

                        $filterProps['CHESS_PRICE_CITYBOX'][] = $appartment['PROPERTIES']['CHESS_PRICE'];
                        $filterProps['CHESS_TOTAL_REDUCED_AREA_CITYBOX'][] = $appartment['PROPERTIES']['CHESS_TOTAL_REDUCED_AREA'];
                     } elseif ($appartment['PROPERTIES']['CHESS_COMMERCIAL'] !== null) {

                        $filterProps['CHESS_PRICE_COMMERCIAL'][] = $appartment['PROPERTIES']['CHESS_PRICE'];
                        $filterProps['CHESS_TOTAL_REDUCED_AREA_COMMERCIAL'][] = $appartment['PROPERTIES']['CHESS_TOTAL_REDUCED_AREA'];
                     } elseif ($appartment['PROPERTIES']['CHESS_CITYBOX'] === null && $appartment['PROPERTIES']['CHESS_COMMERCIAL'] === null) {

                        $filterProps[$code][] = $property;
                     }
                  }
               } else {

                  if ($appartment['PROPERTIES']['CHESS_CITYBOX'] !== null) {

                     $filterProps['CHESS_FLOOR_CITYBOX'][] = $appartment['PROPERTIES']['CHESS_FLOOR'];
                  } elseif ($appartment['PROPERTIES']['CHESS_COMMERCIAL'] !== null) {

                     $filterProps['CHESS_FLOOR_COMMERCIAL'][] = $appartment['PROPERTIES']['CHESS_FLOOR'];
                  } else {

                     $filterProps['CHESS_FLOOR'][] = $property;
                  }
               }
            }


            $arElementProps[$code] = $property;
         }
         $arParams_translit = array("replace_space" => "-", "replace_other" => "-");

         $arLoadProductArray = [
            "IBLOCK_SECTION_ID" => $section['ID'],
            "IBLOCK_ID"         => $this->iblockId,
            "PROPERTY_VALUES"   => $arElementProps,
            "NAME"              => $appartment['NAME'],
            "CODE"              => Cutil::translit($appartment['NAME'], "ru", $arParams_translit),
            "ACTIVE"            => "Y",
            "XML_ID"            => $elementXmlId,
            "DETAIL_PICTURE"    => $appartment['DETAIL_PICTURE']
         ];

         if ($element['ID']) {
            $svgPathProp = $this->CIBlockElement->GetProperty($this->iblockId, $element['ID'], ["sort" => "asc"], ["CODE" => "SVG_FIGURE"])->Fetch();
            if ($svgPathProp) {
               $arLoadProductArray["PROPERTY_VALUES"][$svgPathProp['ID']] = $svgPathProp['VALUE'];
            }

            $svgPathProp2 = $this->CIBlockElement->GetProperty($this->iblockId, $element['ID'], ["sort" => "asc"], ["CODE" => "CHESS_PARKING_PLACE"])->Fetch();

            if ($svgPathProp2) {
               $arLoadProductArray["PROPERTY_VALUES"][$svgPathProp2['ID']] = $svgPathProp2['VALUE'];
            }

            $svgPathProp3 = $this->CIBlockElement->GetProperty($this->iblockId, $element['ID'], ["sort" => "asc"], ["CODE" => "SVG_COMPAS"])->Fetch();

            if ($svgPathProp3) {
               $arLoadProductArray["PROPERTY_VALUES"][$svgPathProp3['ID']] = $svgPathProp3['VALUE'];
            }

            $svgPathProp4 = $this->CIBlockElement->GetProperty($this->iblockId, $element['ID'], ["sort" => "asc"], ["CODE" => "DECOR_STYLES"])->Fetch();

            if ($svgPathProp4) {
               $arLoadProductArray["PROPERTY_VALUES"][$svgPathProp4['ID']] = $svgPathProp4['VALUE'];
            }

            $svgPathProp5 = $this->CIBlockElement->GetProperty($this->iblockId, $element['ID'], ["sort" => "asc"], ["CODE" => "CHECK_PRICE"])->Fetch();

            if ($svgPathProp5) {
               $arLoadProductArray["PROPERTY_VALUES"][$svgPathProp5['ID']] = $svgPathProp5['VALUE'];
               if (!$svgPathProp5['VALUE']) {
                  $arLoadProductArray["PROPERTY_VALUES"]["CHESS_PRICE"] = "";
               }
            }



            $this->CIBlockElement->Update($element['ID'], $arLoadProductArray);
            $PriceProp = $this->CIBlockElement->GetProperty($this->iblockId, $element['ID'], ["sort" => "asc"], ["CODE" => "CHESS_PRICE"])->Fetch();
            if ($PriceProp) {
               $arFields_price = array(
                  "PRODUCT_ID" => $element['ID'],
                  "CATALOG_GROUP_ID" => 1,
                  "PRICE" => $PriceProp['VALUE'],
                  "CURRENCY" => "RUB"
               );

               $price_res = CPrice::GetList(array(), array("PRODUCT_ID" => $element['ID'], "CATALOG_GROUP_ID" => 1))->Fetch();

               if ($price_res) {
                  CPrice::Update($price_res["ID"], $arFields_price);
               } else {
                  CPrice::Add($arFields_price);
               }
            }

            //import more photo 
            $morePhoto = $arElementProps['CHESS_MORE_PHOTO'];

            if ($morePhoto) {
               $this->CIBlockElement->SetPropertyValuesEx($element['ID'], $this->iblockId, ['CHESS_MORE_PHOTO' => ["VALUE" => ['del' => "Y"]]]);
               $this->CIBlockElement->SetPropertyValuesEx($element['ID'], $this->iblockId, ['CHESS_MORE_PHOTO' => $morePhoto]);
            }
         } else {
            $el = $this->CIBlockElement->Add($arLoadProductArray);
            $PriceProp = $this->CIBlockElement->GetProperty($this->iblockId, $el, ["sort" => "asc"], ["CODE" => "CHESS_PRICE"])->Fetch();
            if ($PriceProp) {
               $arFields_price = array(
                  "PRODUCT_ID" => $el,
                  "CATALOG_GROUP_ID" => 1,
                  "PRICE" => $PriceProp['VALUE'],
                  "CURRENCY" => "RUB"
               );

               $price_res = CPrice::GetList(array(), array("PRODUCT_ID" => $el, "CATALOG_GROUP_ID" => 1))->Fetch();

               if ($price_res) {
                  CPrice::Update($price_res["ID"], $arFields_price);
               } else {
                  CPrice::Add($arFields_price);
               }
            }
         }

         // CEventLog::Add(array(
         //     "AUDIT_TYPE_ID" => "SUCCESS1111",
         //     "DESCRIPTION" => $element['ID'],
         // ));
      }
   }

   //import elements without price
   public function importElementsPrice()
   {

      $elements = array();

      $properties = self::request('crm.product.property.list');

      $token = self::getToken('chess@rkmh.ru', 'i2AwX9');

      $ids = array_column($properties, 'ID');

      array_walk($ids, function (&$item1, $key) {
         $item1 = 'PROPERTY_' . $item1;
      });

      for ($i = 1;; $i = ($i + \CRest::BATCH_COUNT)) {
         $resultRequest = \CRest::call(
            'crm.product.list',
            [
               'order'  => ["SORT" => "ASC"],
               'select' => array_merge(['ID', 'NAME', 'PRICE', 'SECTION_ID'], $ids),
               'start' => $i
            ]
         );

         $elements = array_merge($elements, $resultRequest['result']);

         if (!$resultRequest['next'])
            break;
      }

      foreach ($elements as $appartment) {
         $propsWithValues = [];

         foreach ($properties as $property) {

            $propId = 'PROPERTY_' . $property['ID'];

            //list type
            if ($property['PROPERTY_TYPE'] == 'L') {
               $value = $property['VALUES'][$appartment[$propId]['value']]['VALUE'];

               if ($value || $value === '0') {
                  $propertyValue = \CIBlockPropertyEnum::GetList([], ["CODE" => $property['CODE'], 'VALUE' => $value])->Fetch();

                  $propsWithValues[$property['CODE']] = $propertyValue['ID'];
               } else {
                  $propsWithValues[$property['CODE']] = null;
               }
            }
            //others types
            else {
               $propsWithValues[$property['CODE']] = $appartment[$propId]['value'];
            }
         }

         $propsWithValues['CHESS_PRICE'] = $appartment['PRICE'];

         $prepared[] = [
            'ID'              => $appartment['ID'],
            'SECTION_ID'      => $appartment['SECTION_ID'],
            'NAME'            => $appartment['NAME'],
            'PRICE'           => $appartment['PRICE'],
            'PROPERTIES'      => $propsWithValues
         ];
      }

      foreach ($prepared as $appartment) {
         $sectionsXmlId = "b24-section-" . $appartment['SECTION_ID'];
         $elementXmlId  = "b24-element-" . $appartment['ID'];

         $section = $this->CIBlockSection->GetList([], ['XML_ID' => $sectionsXmlId], false, ["ID"])->Fetch();
         $element = $this->CIBlockElement->GetList([], ['XML_ID' => $elementXmlId], false, ["ID"])->Fetch();

         $arElementProps = [];

         foreach ($appartment['PROPERTIES'] as $code => $property) {
            if (
               $code == 'CHESS_PRICE' ||
               $code == 'CHESS_TOTAL_REDUCED_AREA' ||
               $code == 'CHESS_FLOOR'
            ) {

               if ($code == 'CHESS_PRICE' || $code == 'CHESS_TOTAL_REDUCED_AREA') {
                  $statusValueId = $appartment['PROPERTIES']['CHESS_STATUS'];
                  $arPropValue = \CIBlockPropertyEnum::GetByID($statusValueId);

                  if ($arPropValue['XML_ID'] == 'free') {
                     if ($appartment['PROPERTIES']['CHESS_CITYBOX'] !== null) {

                        $filterProps['CHESS_PRICE_CITYBOX'][] = $appartment['PROPERTIES']['CHESS_PRICE'];
                        $filterProps['CHESS_TOTAL_REDUCED_AREA_CITYBOX'][] = $appartment['PROPERTIES']['CHESS_TOTAL_REDUCED_AREA'];
                     } elseif ($appartment['PROPERTIES']['CHESS_COMMERCIAL'] !== null) {

                        $filterProps['CHESS_PRICE_COMMERCIAL'][] = $appartment['PROPERTIES']['CHESS_PRICE'];
                        $filterProps['CHESS_TOTAL_REDUCED_AREA_COMMERCIAL'][] = $appartment['PROPERTIES']['CHESS_TOTAL_REDUCED_AREA'];
                     } elseif ($appartment['PROPERTIES']['CHESS_CITYBOX'] === null && $appartment['PROPERTIES']['CHESS_COMMERCIAL'] === null) {

                        $filterProps[$code][] = $property;
                     }
                  }
               } else {

                  if ($appartment['PROPERTIES']['CHESS_CITYBOX'] !== null) {

                     $filterProps['CHESS_FLOOR_CITYBOX'][] = $appartment['PROPERTIES']['CHESS_FLOOR'];
                  } elseif ($appartment['PROPERTIES']['CHESS_COMMERCIAL'] !== null) {

                     $filterProps['CHESS_FLOOR_COMMERCIAL'][] = $appartment['PROPERTIES']['CHESS_FLOOR'];
                  } else {

                     $filterProps['CHESS_FLOOR'][] = $property;
                  }
               }
            }


            $arElementProps[$code] = $property;
         }
         $arParams_translit = array("replace_space" => "-", "replace_other" => "-");
         // if()
         $arLoadProductArray = [
            "IBLOCK_SECTION_ID" => $section['ID'],
            "IBLOCK_ID"         => $this->iblockId,
            "PROPERTY_VALUES"   => $arElementProps,
            "NAME"              => $appartment['NAME'],
            "CODE"              => Cutil::translit($appartment['NAME'], "ru", $arParams_translit),
            "ACTIVE"            => "Y",
            "XML_ID"            => $elementXmlId,
         ];

         if ($element['ID']) {
            $svgPathProp = $this->CIBlockElement->GetProperty($this->iblockId, $element['ID'], ["sort" => "asc"], ["CODE" => "SVG_FIGURE"])->Fetch();
            if ($svgPathProp) {
               $arLoadProductArray["PROPERTY_VALUES"][$svgPathProp['ID']] = $svgPathProp['VALUE'];
            }

            $svgPathProp2 = $this->CIBlockElement->GetProperty($this->iblockId, $element['ID'], ["sort" => "asc"], ["CODE" => "CHESS_PARKING_PLACE"])->Fetch();

            if ($svgPathProp2) {
               $arLoadProductArray["PROPERTY_VALUES"][$svgPathProp2['ID']] = $svgPathProp2['VALUE'];
            }

            $svgPathProp3 = $this->CIBlockElement->GetProperty($this->iblockId, $element['ID'], ["sort" => "asc"], ["CODE" => "SVG_COMPAS"])->Fetch();

            if ($svgPathProp3) {
               $arLoadProductArray["PROPERTY_VALUES"][$svgPathProp3['ID']] = $svgPathProp3['VALUE'];
            }

            $svgPathProp4 = $this->CIBlockElement->GetProperty($this->iblockId, $element['ID'], ["sort" => "asc"], ["CODE" => "DECOR_STYLES"])->Fetch();

            if ($svgPathProp4) {
               $arLoadProductArray["PROPERTY_VALUES"][$svgPathProp4['ID']] = $svgPathProp4['VALUE'];
            }

            $svgPathProp5 = $this->CIBlockElement->GetProperty($this->iblockId, $element['ID'], ["sort" => "asc"], ["CODE" => "CHECK_PRICE"])->Fetch();



            if ($svgPathProp5) {
               $arLoadProductArray["PROPERTY_VALUES"][$svgPathProp5['ID']] = $svgPathProp5['VALUE'];

               if (!$svgPathProp5['VALUE']) {
                  $arLoadProductArray["PROPERTY_VALUES"]["CHESS_PRICE"] = "";
               }
            }



            $this->CIBlockElement->Update($element['ID'], $arLoadProductArray);

            $PriceProp = $this->CIBlockElement->GetProperty($this->iblockId, $element['ID'], ["sort" => "asc"], ["CODE" => "CHESS_PRICE"])->Fetch();
            if ($PriceProp) {
               $arFields_price = array(
                  "PRODUCT_ID" => $element['ID'],
                  "CATALOG_GROUP_ID" => 1,
                  "PRICE" => $PriceProp['VALUE'],
                  "CURRENCY" => "RUB"
               );

               $price_res = CPrice::GetList(array(), array("PRODUCT_ID" => $element['ID'], "CATALOG_GROUP_ID" => 1))->Fetch();

               if ($price_res) {
                  CPrice::Update($price_res["ID"], $arFields_price);
               } else {
                  CPrice::Add($arFields_price);
               }
            }
         } else {
            $el = $this->CIBlockElement->Add($arLoadProductArray);
            $PriceProp = $this->CIBlockElement->GetProperty($this->iblockId, $el, ["sort" => "asc"], ["CODE" => "CHESS_PRICE"])->Fetch();
            if ($PriceProp) {
               $arFields_price = array(
                  "PRODUCT_ID" => $el,
                  "CATALOG_GROUP_ID" => 1,
                  "PRICE" => $PriceProp['VALUE'],
                  "CURRENCY" => "RUB"
               );

               $price_res = CPrice::GetList(array(), array("PRODUCT_ID" => $el, "CATALOG_GROUP_ID" => 1))->Fetch();

               if ($price_res) {
                  CPrice::Update($price_res["ID"], $arFields_price);
               } else {
                  CPrice::Add($arFields_price);
               }
            }
         }
      }

      // Список ЖК
      $resCompl =  $this->CIBlockElement->GetList(array(), array("IBLOCK_ID" => 18), false, false, array("ID", "NAME"));
      while ($obCompl = $resCompl->GetNextElement()) {
         $arFieldsComplex = $obCompl->GetFields();
         $this->CIBlockElement->Update($arFieldsComplex['ID'], array('NAME' => html_entity_decode($arFieldsComplex['NAME'])));
      }
   }
}
