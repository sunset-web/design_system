<?
header('Content-Type: application/json; charset=utf-8');
echo json_encode(array(
   'componentName' => $this->getComponent()->getName(),
   'iblockId' => $arParams['IBLOCK_ID'],
   'elementId' => $arParams['ELEMENT_ID'],
   'arResult' => $arResult,
   'componentId' => $arResult['COMPONENT_ID'],
   'scriptCast' => base64_encode($arResult['SCRIPT_PATH']),
));
