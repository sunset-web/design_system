<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);
if (!\Bitrix\Main\Loader::includeModule('iblock'))
   return;
$signer = new \Bitrix\Main\Security\Sign\Signer;
try {
   $paramString = $signer->unsign($request->get('signedParameters') ?: '', $request->get('templateName'));
} catch (\Bitrix\Main\Security\Sign\BadSignatureException $e) {
   die();
}
$parameters = unserialize(base64_decode($paramString), ['allowed_classes' => false]);
// вызов компонента
$APPLICATION->IncludeComponent(
   $request->get('componentName'),
   $request->get('templateName'),
   $parameters,
   false
);
