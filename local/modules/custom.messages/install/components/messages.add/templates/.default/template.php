<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Component\ParameterSigner;
?>
<? $sign = ParameterSigner::signParameters($this->getComponent()->getName(), ['HLBLOCKS_ID' => $arParams['HLBLOCKS_ID']]); ?>
<div>
	<label for="text"><?= Loc::getMessage('MESSAGE_LABEL_TEXT') ?>:
		<textarea name="text" id="text"></textarea>
	</label>
	<p><a class="add_message--js" href="javascript:void(0)" data-param="<?= $sign ?>"><?= Loc::getMessage('MESSAGE_SUBMIT') ?></a></p>
</div>