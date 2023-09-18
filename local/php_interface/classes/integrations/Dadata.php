<?
// Интеграция с dadata, получение данных по долготе и широте
// Работает через curl
class Dadata
{
   function init($options, $link)
   {
      $token = 'your_token';
      $ch = curl_init();
      curl_setopt_array($ch, array(
         CURLOPT_RETURNTRANSFER => TRUE,
         CURLOPT_POST => TRUE,
         CURLOPT_SSL_VERIFYPEER => FALSE,
         CURLOPT_SSL_VERIFYHOST => 0,
         CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
         CURLOPT_ENCODING => 'gzip',
         CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json; charset=utf-8',
            "Authorization: Token " . $token,
         )
      ));
      curl_setopt_array($ch, array(
         CURLOPT_URL => $link,
         CURLOPT_POSTFIELDS => json_encode($options),
      ));
      $resulte = curl_exec($ch);
      curl_close($ch);
      return json_decode($resulte, true);
   }

   function getDadata($lat, $lon)
   {
      $options = array(
         "lat" => $lat,
         "lon" => $lon,
         "count" => "1",
      );
      $result = self::init($options, 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/geolocate/address');
      $data = $result['suggestions'][0]['data'];
      $city = $data['city'];
      $region = $data['region'];
      return (array($city, $region));
   }
}
