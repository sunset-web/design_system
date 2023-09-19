<?
// Класс реализует рассчет расстояние между пользователем и точками
class CalculateGeolocation
{
   // инициализация входных данных
   function __construct(string $GEOLOCATION)
   {
      CModule::IncludeModule("iblock");
      $this->SESSION = \Bitrix\Main\Application::getInstance()->getSession();

      $this->CIBlockElement  = new \CIBlockElement;
      $this->IBLOCK_ID = 5;

      $this->GEOLOCATION  = $GEOLOCATION;
      // Дистанция измеряется в км
      $this->DISTANCE = 30;
      // Радиус планеты
      $this->RADIUS = 6372.797;
      // 1 градус по широте
      $this->DISTANCE_LAT = 111.3;
      // 1 градус по долготе высчитывается от текущей геопозиции
      $this->DISTANCE_DOLG = 0;
      // Средняя скорость передвижения м в мин (=5 км в час)
      $this->SPEED = 83.3334;
      // Среднее отклонение по времени в %
      $this->PERCENT = 0.15;
      // Ключ геокодера
      $this->key = 'e2ae43d9-0c0a-49c7-9736-80d055e3dcf2';
   }
   // Разбивает строчную координату
   protected function ConvertGeoStringToArray(string $geo)
   {

      return explode(',', $geo);
   }
   // Рассчитывает дистанцию долготы
   protected function CalculateDolg(float $dolg)
   {

      $this->DISTANCE_DOLG = cos(deg2rad($dolg)) * $this->DISTANCE_LAT;
   }
   // Изменяет расстояние рассчета
   public function ChangeDistance(int $distance)
   {

      $this->DISTANCE = $distance;
   }
   // Переводит растояние в градусы
   protected function ConvertDistanceKmToGrad()
   {

      $listGrad = [];

      // широта
      $listGrad[0] = $this->DISTANCE / $this->DISTANCE_LAT;
      // долгота
      $listGrad[1] = $this->DISTANCE / $this->DISTANCE_DOLG;

      return $listGrad;
   }
   // Рассчитывает пограничные координаты
   protected function CalculateGeo()
   {

      $listCoordinates = self::ConvertGeoStringToArray($this->GEOLOCATION);

      self::CalculateDolg($listCoordinates[1]);

      $listGrad = self::ConvertDistanceKmToGrad();

      $listBorderCoordinates = [];
      // Расчет координат
      $listBorderCoordinates['LAT']['MIN'] = $listCoordinates[0] - $listGrad[0];
      $listBorderCoordinates['LAT']['MAX'] = $listCoordinates[0] + $listGrad[0];
      $listBorderCoordinates['DOLG']['MIN'] = $listCoordinates[1] - $listGrad[0];
      $listBorderCoordinates['DOLG']['MAX'] = $listCoordinates[1] + $listGrad[0];

      return $listBorderCoordinates;
   }
   // Получает список точек
   protected function GetDataBase()
   {

      $listPlaces = [];
      $listBorderCoordinates = self::CalculateGeo();

      $query = $this->CIBlockElement->GetList(
         [],
         [
            "IBLOCK_ID" => $this->IBLOCK_ID,
            [
               "LOGIC" => "AND",
               [">PROPERTY_LAT" => $listBorderCoordinates['LAT']['MIN'], "<PROPERTY_LAT" => $listBorderCoordinates['LAT']['MAX']],
               [">PROPERTY_DOLG" => $listBorderCoordinates['DOLG']['MIN'], "<PROPERTY_DOLG" => $listBorderCoordinates['DOLG']['MAX']]
            ]
         ],
         false,
         false,
         ["PROPERTY_COORD", "ID"]
      );
      while ($item = $query->GetNextElement()) {
         $arFields = $item->GetFields();
         $listPlaces[$arFields['ID']] = $arFields['PROPERTY_COORD_VALUE'];
      }

      return $listPlaces;
   }
   // Рассчитывает расстояние до точек
   protected function CalculateDistanceToGeo(float $latitude1, float $longitude1, float $latitude2, float $longitude2)
   {

      $dLat = deg2rad($latitude2 - $latitude1);
      $dLon = deg2rad($longitude2 - $longitude1);

      $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon / 2) * sin($dLon / 2);
      $c = 2 * asin(sqrt($a));
      $d = $this->RADIUS * $c;

      return $d;
   }
   // Рассчитывает время по расстоянию
   protected function CalculateTimeToDistance(float $distance)
   {

      $time = round((($distance * 1000 / $this->SPEED) + (($distance * 1000 / $this->SPEED) * $this->PERCENT)), 0);

      return $time > 60 ? "more" : $time;
   }
   // Сортировка результата по удаленности
   protected function SortResult(array $array)
   {

      $moreArray = [];

      // Убираем значение more
      foreach ($array as $key => $value) {

         if ($value === 'more') {

            $moreArray[$key] = $value;

            unset($array[$key]);
         }
      }

      asort($array);

      return $array + $moreArray;
   }
   // Получаем адрес по координатам
   public function GetAddressFromCoord(float $long, float $lat)
   {
      $address = 'https://geocode-maps.yandex.ru/1.x/?apikey=' . $this->key . '&lang=ru_RU&format=json&geocode=' . $long . ',' . $lat;
      $putdata = json_decode(file_get_contents($address));

      $this->SESSION->set('address_name', $putdata->response->GeoObjectCollection->featureMember[0]->GeoObject->name);
   }
   // Формируем массив и записываем в сессию
   public function SetSessionGeo()
   {

      $listPlaces = self::GetDataBase();
      $listCoordinates = self::ConvertGeoStringToArray($this->GEOLOCATION);
      $result = [];

      foreach ($listPlaces as $id => $coord) {
         $coordArr = self::ConvertGeoStringToArray($coord);
         $distance = self::CalculateDistanceToGeo($listCoordinates[0], $listCoordinates[1], $coordArr[0], $coordArr[1]);
         $result[$id] = self::CalculateTimeToDistance($distance);
      }

      $result = self::SortResult($result);

      $this->SESSION->set('places', $result);
      return;
   }
}
