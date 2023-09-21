<?
AddEventHandler("main", "OnBeforeUserLogin", array("LoginPhoneMail", "OnBeforeUserLogin"));
AddEventHandler("main", "OnBeforeUserRegister", array("LoginPhoneMail", "OnBeforeUserRegister"));
AddEventHandler("main", "OnBeforeUserUpdate", array("LoginPhoneMail", "OnBeforeUserUpdate"));
// Класс реализует подмену логина на почту и телефон
// И генерирует автоматический логин пользователя
class LoginPhoneMail
{
   static function OnBeforeUserLogin($arFields)
   {

      if (stripos($arFields["LOGIN"], "@") !== false) {
         $rsUsers = CUser::GetList(($by = "LAST_NAME"), ($order = "asc"), array("EMAIL" => $arFields["LOGIN"]));
         if ($user = $rsUsers->GetNext())
            $arFields["LOGIN"] = $user["LOGIN"];
      } else {
         $phone = Bitrix\Main\UserPhoneAuthTable::normalizePhoneNumber($arFields['LOGIN']);
         $user = \Bitrix\Main\UserPhoneAuthTable::getList($parameters = array(
            'filter' => array('PHONE_NUMBER' => $phone)
         ));
         if ($row = $user->fetch()) {
            $rsUser = CUser::GetByID($row['USER_ID']);
            $arUser = $rsUser->Fetch();
            $arFields['LOGIN'] = $arUser['LOGIN'];
         }
      }
   }

   static function OnBeforeUserRegister(&$arFields)
   {
      $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

      function generate_string($input, $strength = 16)
      {
         $input_length = strlen($input);
         $random_string = '';
         for ($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
         }

         return $random_string;
      }
      $arFields["LOGIN"] = generate_string($permitted_chars, 10);
      $arFields["PERSONAL_PHONE"] = $arFields["PHONE_NUMBER"];
      return $arFields;
   }

   static function OnBeforeUserUpdate(&$arFields)
   {

      $arFields["PERSONAL_PHONE"] = $arFields["PHONE_NUMBER"];
      return $arFields;
   }
}
