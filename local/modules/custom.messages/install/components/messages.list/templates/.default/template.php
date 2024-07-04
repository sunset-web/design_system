<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Component\ParameterSigner;

?>
<? if (!empty($arResult['MESSAGES'])) : ?>
	<ul>
		<? foreach ($arResult['MESSAGES'] as $key => $message) : ?>
			<li>
				<div>
					<? $sign = ParameterSigner::signParameters($this->getComponent()->getName() . '_custom', ['ID' => $message['ID'], 'HLBLOCKS_ID' => $arParams['HLBLOCKS_ID']]); ?>
					<? if (!empty($message['USER_LAST_NAME']) || !empty($message['USER_NAME'])) : ?>
						<p><?= Loc::getMessage('USER_NAME') ?>: <?= $message['USER_LAST_NAME'] ?> <?= $message['USER_NAME'] ?></p>
					<? endif ?>
					<? if (!empty($message['UF_TEXT'])) : ?>
						<p><?= Loc::getMessage('MESSAGE_TEXT') ?>: <?= $message['UF_TEXT'] ?></p>
					<? endif ?>
					<? if (!empty($message['UF_DATE'])) : ?>
						<p><?= Loc::getMessage('MESSAGE_DATE') ?>: <?= $message['UF_DATE'] ?></p>
					<? endif ?>
					<? if ($message['CHANGE_FLAG'] == 'Y') : ?>
						<div>
							<p><a class="change_message--js" href="javascript:void(0)"><?= Loc::getMessage('MESSAGE_CHANGE_BTN') ?></a></p>
							<div class="textarea_send--js hidden">
								<textarea name="change_text"><?= $message['UF_TEXT'] ?></textarea>
								<p><a class="send_message--js" href="javascript:void(0)" data-param="<?= $sign ?>"><?= Loc::getMessage('MESSAGE_CHANGE_BTN') ?></a></p>
							</div>
						</div>

					<? endif ?>
					<? if ($message['DELETE_FLAG'] == 'Y') : ?>
						<p><a class="delete_message--js" href="javascript:void(0)" data-param="<?= $sign ?>"><?= Loc::getMessage('MESSAGE_DELETE_BTN') ?></a></p>
					<? endif ?>
				</div>
			</li>
		<? endforeach ?>
	</ul>
	<? if ($arParams['SHOW_PAGER'] == 'Y') : ?>
		<div>
			<? $APPLICATION->IncludeComponent(
				"bitrix:main.pagenavigation",
				"",
				array(
					"NAV_OBJECT" => $arResult['NAV'],
					"SEF_MODE" => "N",
				),
				false
			); ?>
		</div>
	<? endif ?>
<? else : ?>
	<p><?= Loc::getMessage('MESSAGES_EMPTY') ?></p>
<? endif ?>