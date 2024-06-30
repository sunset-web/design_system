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
$this->addExternalJS($this->GetFolder() . "/inputmask.js");
?>

<? if ($arResult["OK_MESSAGE"] <> '') { ?>
	<? LocalRedirect($arParams["LINK_TO_THANK"]); ?>
<? } ?>

<div class="pop-up-request__container">
	<div class="pop-up-request__wrapper">
		<div style="display:none;">
			<? echo '<pre>'; ?>
			<? print_r($arResult['FIELDS']); ?>
			<? echo '</pre>'; ?>
		</div>
		<? if ($arResult["TITLE_FORM"]) : ?>
			<h2 class="title"><?= $arResult["TITLE_FORM"]; ?></h2>
		<? endif ?>
		<? if ($_REQUEST["user_title"]) : ?>
			<h3 class="subtitle"><?= $_REQUEST["user_title"] ? $_REQUEST["user_title"] : "" ?></h3>
		<? endif; ?>


		<form class="consultation__form" action="<?= POST_FORM_ACTION_URI ?>" method="POST" novalidate="novalidate">
			<?= bitrix_sessid_post() ?>
			<div class="consultation__form-wrapper">


				<? if (!empty($arResult["ERROR_MESSAGE"])) { ?>
					<script>
						<? foreach ($arResult["ERROR_MESSAGE"] as $key => $err) : ?>
							$('input[name="<?= $key ?>"]').addClass('error_input');
							$('input[name="<?= $key ?>"]').parent().append('<span class="input-error__span"><?= $err ?></span>');
						<? endforeach ?>
						setTimeout(() => {
							$('.consultation__form-input').removeClass('error_input');
							$('.input-error__span').remove();
						}, 4000);
					</script>
				<? } ?>

				<div class="consultation__form-wrapper-item">
					<div class="consultation__input-wrapper">

						<input id="form-name-order" class="consultation__form-input" type="text" <? if (empty($arResult["REQUIRED_FIELDS"]) || in_array("NAME", $arResult["REQUIRED_FIELDS"])) : ?>required="" data-validate-field="name" <? endif ?> name="user_name" value="<?= $arResult["user_name"] ?>" placeholder="<?= GetMessage("MFT_NAME") ?>">
						<label for="form-name-order" class="consultation__label-name">
							<svg width="12" height="14" viewBox="0 0 12 	14" fill="none" xmlns="http://www.w3.org/2000/svg">
								<g opacity="0.8">
									<path fill-rule="evenodd" clip-rule="evenodd" d="M8.52005 3.57143C8.52005 4.99171 7.39193 6.14285 6.00005 6.14285C4.60817 6.14285 3.48005 4.99171 3.48005 3.57143C3.48005 2.15114 4.60817 1 6.00005 1C7.39193 1 8.52005 2.15114 8.52005 3.57143Z" stroke="#537791" stroke-linecap="square" />
									<path fill-rule="evenodd" clip-rule="evenodd" d="M10.2001 13H1.80005C1.80005 12.391 1.80005 11.8116 1.80005 11.287C1.80005 9.86587 2.92829 8.71429 4.32005 8.71429H7.68005C9.07181 8.71429 10.2001 9.86587 10.2001 11.287C10.2001 11.8116 10.2001 12.391 10.2001 13Z" stroke="#537791" stroke-linecap="square" />
								</g>
							</svg>
						</label>


					</div>
					<div class="consultation__input-wrapper">

						<input id="form-phone-order" class="consultation__form-input consultation__form-input-phone" pattern=".{3,4}" inputmode="numeric" type="tel" name="user_phone" <? if (empty($arResult["REQUIRED_FIELDS"]) || in_array("user_phone", $arResult["REQUIRED_FIELDS"])) : ?>required data-validate-field="tel" <? endif ?> value="<?= $arResult["user_phone"] ?>" placeholder="<?= GetMessage("MFT_PHONE") ?>">
						<label for="form-phone-order" class="consultation__label-phone">
							<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
								<g opacity="0.8" clip-path="url(#clip0_2_333)">
									<path d="M3.7754 0.400024H2.00002C1.11637 0.400024 0.400024 1.11637 0.400024 2.00002V3.60002C0.400024 8.0183 3.98175 11.6 8.40003 11.6H10C10.8837 11.6 11.6 10.8837 11.6 10V8.89445C11.6 8.59143 11.4288 8.31442 11.1578 8.17891L9.22505 7.21254C8.78961 6.99482 8.26229 7.21324 8.10833 7.6751L7.87012 8.38974C7.71582 8.85265 7.24097 9.12821 6.76249 9.03252C4.84746 8.64951 3.35054 7.15259 2.96753 5.23756C2.87184 4.75908 3.1474 4.28423 3.61031 4.12993L4.49722 3.83429C4.89379 3.7021 5.12174 3.28687 5.02035 2.88132L4.55152 1.006C4.46248 0.649862 4.1425 0.400024 3.7754 0.400024Z" stroke="#537791" />
								</g>
								<defs>
									<clipPath>
										<rect width="12" height="12" fill="white" />
									</clipPath>
								</defs>
							</svg>
						</label>


					</div>

				</div>

				<div class="consultation__form-wrapper-item">
					<div class="consultation__textarea-wrapper">

						<textarea id="form-comm-order" name="MESSAGE" placeholder="<?= GetMessage("MFT_MESSAGE") ?>" class="consultation__form-input consultation__form-textarea" <? if (empty($arResult["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arResult["REQUIRED_FIELDS"])) : ?>data-validate-field="MESSAGE" required="" <? endif ?>><?= $arResult["MESSAGE"] ?></textarea>
						<label for="form-comm-order" class="consultation__label-textarea">
							<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
								<g opacity="0.8" clip-path="url(#clip0_2_337)">
									<path d="M0.800391 8.00078L0.446837 7.64723L0.322817 7.77125L0.303449 7.94557L0.800391 8.00078ZM8.00039 0.800781L8.35394 0.447228C8.26018 0.35346 8.133 0.300781 8.00039 0.300781C7.86778 0.300781 7.74061 0.35346 7.64684 0.447228L8.00039 0.800781ZM11.2004 4.00078L11.5539 4.35433C11.7492 4.15907 11.7492 3.84249 11.5539 3.64723L11.2004 4.00078ZM4.00039 11.2008L4.05561 11.6977L4.22992 11.6784L4.35394 11.5543L4.00039 11.2008ZM0.400391 11.6008L-0.0965512 11.5456C-0.113325 11.6965 -0.0605665 11.8469 0.0468372 11.9543C0.154241 12.0617 0.304644 12.1145 0.455606 12.0977L0.400391 11.6008ZM1.15394 8.35434L8.35394 1.15433L7.64684 0.447228L0.446837 7.64723L1.15394 8.35434ZM7.64684 1.15433L10.8468 4.35433L11.5539 3.64723L8.35394 0.447228L7.64684 1.15433ZM10.8468 3.64723L3.64684 10.8472L4.35394 11.5543L11.5539 4.35433L10.8468 3.64723ZM3.94517 10.7038L0.345175 11.1038L0.455606 12.0977L4.05561 11.6977L3.94517 10.7038ZM0.897332 11.656L1.29733 8.056L0.303449 7.94557L-0.0965512 11.5456L0.897332 11.656ZM5.24684 3.55433L8.44684 6.75433L9.15394 6.04723L5.95394 2.84723L5.24684 3.55433ZM5.60039 12.1008H12.0004V11.1008H5.60039V12.1008Z" fill="#537791" />
								</g>
								<defs>
									<clipPath>
										<rect width="12" height="12" fill="white" />
									</clipPath>
								</defs>
							</svg>
						</label>

					</div>
				</div>

			</div>
			<div class="consultation__buttons">
				<div class="btn-wrapper">

					<input type="hidden" name="user_title" id="title_form_modal_order" value="<?= $_REQUEST["user_title"] ? $_REQUEST["user_title"] : "" ?>">
					<input type="hidden" name="user_security" class="input_security" value="">
					<input type="hidden" name="PARAMS_HASH" value="<?= $arResult["PARAMS_HASH"] ?>">

					<input type="submit" name="submit" class="btn" value="<?= GetMEssage("MFT_SUBMIT"); ?>">
				</div>
				<? if ($arResult["POLICY_TEXT"]) : ?>
					<div class="consultation__agreement">
						<?= $arResult["POLICY_TEXT"]; ?>
					</div>
				<? endif ?>


			</div>
		</form>
	</div>

	<a href="javascript:void(0)" data-fancybox-close class="btn-close"></a>
	<script>
		if (typeof hiddenInputDown != 'function') {
			function hiddenInputDown() {
				$(document).ready(function() {
					document.querySelectorAll('input[type="tel"]').forEach(input => {
						$(document).ready(function() {
							Inputmask("+7 (999) 999-99-99", {
								showMaskOnHover: false,
							}).mask(input);
						});
					});
					setTimeout(
						function() {
							$('.input_security').val('promgran');
						}, 4000);
				});
			}
		}
		hiddenInputDown();
	</script>
</div>