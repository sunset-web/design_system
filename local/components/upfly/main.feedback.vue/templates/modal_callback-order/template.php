<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
?>
<? \Bitrix\Main\UI\Extension::load("upfly.callback-order"); ?>
<div id="callback-order"></div>
<script>
	BX.CallbackOrderResult = <?= json_encode($arResult) ?>;
	BX.CallbackOrderParams = <?= json_encode($arParams) ?>;
	app = new BX.Upfly.CallbackOrder('#callback-order')
	app.init()
</script>