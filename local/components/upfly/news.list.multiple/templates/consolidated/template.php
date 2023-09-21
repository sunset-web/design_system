<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
\Bitrix\Main\Page\Asset::getInstance()->addCss($this->GetFolder() . '/styles/budget-consolidated.css', true);

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
$months = array(1 => 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');
$curYear = $_REQUEST['filter_date'] ? date('Y', strtotime($_REQUEST['filter_date'])) : date('Y');
$curMonth = $_REQUEST['filter_date'] ? date('n', strtotime($_REQUEST['filter_date'])) : date('n');
?>
<div class="overlay overlay-desktop overlay-desktop-js"></div>
<!-- "Начало модального окна "Три точки" " -->
<div class="modal-menu modal-settings-js">
	<div class="modal-menu-header">
	</div>
	<div class="modal-menu-item menu-close-js">
		<a href="/personal/equipment/" title="<?= GetMessage('LINK_ELEMENT_EDIT') ?>"><?= GetMessage('LINK_ELEMENT_EDIT') ?></a>
	</div>
</div>
<!-- "Конец модального окна "Три точки"" -->

<!-- "Начало модального окна количества строк на странице " -->
<div class="row-count-menu modal-menu">
	<div class="row-count-menu-header modal-menu-header">
		<?= GetMessage('DISPLAY_BY') ?>
	</div>
	<div class="row-count-menu-item modal-menu-item row-count-menu-close-js">
		<a href="javascript:void(0)"><?= $arParams['NEWS_START_COUNT'] ?></a>
	</div>
	<div class="row-count-menu-item modal-menu-item row-count-menu-close-js">
		<a href="javascript:void(0)"><?= $arParams['NEWS_START_COUNT'] * 2 ?></a>
	</div>
	<div class="row-count-menu-item modal-menu-item row-count-menu-close-js">
		<a href="javascript:void(0)"><?= $arParams['NEWS_START_COUNT'] * 4 ?></a>
	</div>
</div>
<!-- "Конец модального окна количества строк на странице " -->

<!-- НАЧАЛО МОДАЛЬНОЕ ОКНО ЗАГРУЗКА -->
<div class="modal-loading-result flex fd-column ai-center">
	<div class="modal-loading-result__title tac"><?= GetMessage('SUCCESS_LOAD') ?></div>
	<p class="tac small">
		<?= \Bitrix\Main\Localization\Loc::getMessage('LOAD_TEXT', array('#COUNT#' => 10));
		// todo добавить количество
		?>
	</p>
	<button class="btn-blue p13-24"><?= GetMessage('NEXT_WORKING') ?></button>
</div>
<!-- КОНЕЦ МОДАЛЬНОЕ ОКНО ЗАГРУЗКА -->

<!-- НАЧАЛО МОДАЛЬНОЕ ОКНО УДАЛЕНИЕ -->
<div class="modal-delete-confirm flex fd-column ai-center">
	<div class="modal-delete-confirm__title tac"><?= GetMessage('DELETE_TITLE') ?></div>
	<p class="tac small"><?= GetMessage('DELETE_TEXT') ?></p>
	<div class="modal-delete-confirm__button-group flex fd-column-xs">
		<button class="btn-red p13-24"><?= GetMessage('DELETE') ?></button>
		<button class="btn-white p13-24"><?= GetMessage('CANCEL') ?></button>
	</div>
</div>
<!-- КОНЕЦ МОДАЛЬНОЕ ОКНО УДАЛЕНИЕ -->

<!-- уведомление о добавлении комментария -->
<!-- active-notification -->
<div class="comment-notification small comment-notification-js">
	<?= GetMessage('COMMENT_NOTIFICATION') ?>
</div>
<!-- НАЧАЛО МОДАЛЬНОЕ ОКНО КОММЕНТАРИИ -->
<div class="modal-comments flex fd-column ai-center">
	<h4 class="modal-comments__title"></h4>
	<button class="modal-close-btn modal-close-btn--js"></button>
	<ul class="modal-comments__list">
	</ul>
	<div class="modal-comments__functional">
		<textarea name="new-comment" placeholder="Новый комментарий..."></textarea>

		<div class="modal-comments__button-group flex fd-column-xs">
			<button type="submit" class="btn-blue p13-24 comment-js-create-btn"><?= GetMessage('SEND') ?></button>
			<button class="btn-white p13-24"><?= GetMessage('CANCEL') ?></button>
		</div>
	</div>
</div>
<!-- КОНЕЦ МОДАЛЬНОЕ ОКНО КОММЕНТАРИИ -->

<div class="top-panel flex container container-small">
	<div class="top-panel__selector-group flex">
		<div class="__select __select-search __select-checkbox-js  __select-checkbox-search-js companies_filter-js" data-state="">
			<div class="__select__title __select__title-search" data-default="<?= GetMessage('ALL_COMPANIES') ?>">
				<span class="__select__title__span"><?= GetMessage('ALL_COMPANIES') ?></span>
			</div>
			<div class="__select__content __select__content-search search-wrapper-js">

				<div class="__search-wrapper ">
					<input type="search" placeholder="Поиск названию " class="search--js" value="">
					<button class="__search-clear-btn search-clear-btn--js">
						<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M8.59093 7L13.0441 2.54687C13.2554 2.3359 13.3743 2.04961 13.3745 1.75099C13.3748 1.45237 13.2564 1.16587 13.0455 0.95453C12.8345 0.743185 12.5482 0.624305 12.2496 0.624041C11.951 0.623778 11.6645 0.742152 11.4531 0.953123L7 5.40625L2.54687 0.953123C2.33553 0.741779 2.04888 0.623047 1.75 0.623047C1.45111 0.623047 1.16447 0.741779 0.953123 0.953123C0.741779 1.16447 0.623047 1.45111 0.623047 1.75C0.623047 2.04888 0.741779 2.33553 0.953123 2.54687L5.40625 7L0.953123 11.4531C0.741779 11.6645 0.623047 11.9511 0.623047 12.25C0.623047 12.5489 0.741779 12.8355 0.953123 13.0469C1.16447 13.2582 1.45111 13.3769 1.75 13.3769C2.04888 13.3769 2.33553 13.2582 2.54687 13.0469L7 8.59375L11.4531 13.0469C11.6645 13.2582 11.9511 13.3769 12.25 13.3769C12.5489 13.3769 12.8355 13.2582 13.0469 13.0469C13.2582 12.8355 13.3769 12.5489 13.3769 12.25C13.3769 11.9511 13.2582 11.6645 13.0469 11.4531L8.59093 7Z" fill="#C6CBD6" />
						</svg>
					</button>
				</div>

				<div class="search-checkbox-wrapper __filter" data-filter="PROPERTY_COMPANY">
					<? global $keyCur;
					$companyArrCur = explode('_', $_REQUEST['filter_company']) ?: array();
					foreach ($arResult['COMPANIES'] as $company) {
						$keyCur = $company['ID'];
						$checked = reset(array_filter($companyArrCur, function ($e) {
							global $keyCur;
							return $e == $keyCur;
						})) ? true : false ?>
						<div class="checkbox_wrapper">
							<label class="custom-checkbox __select__label __select__label-search">
								<input class="<?= $checked ? "selected" : "" ?>" type="checkbox" value="<?= $company['ID'] ?>" <?= $checked ? "checked" : "" ?>>
								<span class="checkbox_content" title="<?= htmlspecialchars($company['NAME']) ?>"><?= $company['NAME'] ?></span>
							</label>
						</div>
					<? } ?>
				</div>
			</div>
		</div>


		<div class="__select __select__year __select-form-js date-year_filter-js" data-date="<?= $curYear ?>">
			<div class="__select__title text-black"><?= $curYear . ' год' ?></div>
			<div class="__select__content ">
				<div class="wrapper">
					<? for ($i = $curYear - 5; $i < $curYear + 5; $i++) { ?>
						<input id="year<?= $i ?>" value="<?= $i ?>" class="__select__input" type="radio">
						<label for="year<?= $i ?>" class="__select__label">
							<?= $i . ' год' ?></label>
					<? } ?>
				</div>
			</div>
		</div>

		<div class="__select __select__month __select-form-js date-month_filter-js" data-date="<?= str_pad($curMonth, 2, '0', STR_PAD_LEFT) ?>">
			<div class="__select__title text-black"><?= $months[$curMonth] ?></div>
			<div class="__select__content ">
				<div class="wrapper">
					<? foreach ($months as $key => $name) { ?>
						<input id="month<?= $key ?>" value="<?= str_pad($key, 2, '0', STR_PAD_LEFT) ?>" class="__select__input" type="radio">
						<label for="month<?= $key ?>" class="__select__label">
							<?= $name ?></label>
					<? } ?>
				</div>
			</div>
		</div>
	</div>

	<div class="top-panel__total flex-s ai-center">
		<div class="small">
			<?= \Bitrix\Main\Localization\Loc::getMessage('ALL_SUMM', array('#MONTH#' => $months[$curMonth], "#YEAR#" => $curYear)); ?>
		</div>
		<div class="sum"><?= number_format((float)$arResult['ALL_SUMM'], 0, '', ' ') . "₽"; ?></div>
	</div>
</div>

<div class="table-wrapper container-small container-small_table">
	<div class="table-container">
		<!-- НАДПИСЬ ПРИ ОТСУТСТВИИ ЭЛЕМЕНТОВ -->
		<div class="filter-not-found <?= reset($arResult["ITEMS"]) ? "hidden" : "" ?>"><?= GetMessage('ELEMENTS_NO_FIND') ?></div>

		<table class="small" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th></th>
					<th class="checkbox-field checkbox-all-js">
						<label class="custom-checkbox">
							<input type="checkbox" name="" id="">
							<span></span>
						</label>
					</th>
					<th class="stretch-130" data-sort="NAME" title="<?= GetMessage('LABEL_NAME') ?>"><?= GetMessage('LABEL_NAME') ?></th>
					<th class="fix-200" data-sort="COMPANY" title="<?= GetMessage('LABEL_COMPANY') ?>"><?= GetMessage('LABEL_COMPANY') ?></th>
					<!-- <th class="fix-150" data-sort="TYPE" title="<? //= GetMessage('LABEL_TYPE') 
																						?>"><? //= GetMessage('LABEL_TYPE') 
																																	?></th> -->
					<th class="fix-150" data-sort="PRICE" title="<?= GetMessage('LABEL_SUMM') ?>"><?= GetMessage('LABEL_SUMM') ?></th>
					<th class="fix-150" data-sort="IBLOCK_ID" title="<?= GetMessage('LABEL_SECTION') ?>"><?= GetMessage('LABEL_SECTION') ?></th>
					<th></th>
				</tr>
			</thead>

			<tbody id="ajaxUpdateList">
				<? foreach ($arResult["ITEMS"] as $arItem) : ?>
					<?
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					?>
					<tr data-index="<?= $arItem['ID']; ?>" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
						<td></td>
						<td class="checkbox-field">
							<label class="custom-checkbox">
								<input type="checkbox" data-id="<?= $arItem['ID'] ?>">
								<span></span>
							</label>
						</td>
						<td class="stretch-130"> <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>" title="<?= $arItem['NAME']; ?>"><?= $arItem['NAME']; ?></a></td>
						<td class="fix-200" title="<? $GLOBALS['arItemFilter'] = $arItem;
															echo htmlspecialchars(reset(array_filter($arResult['COMPANIES'], function ($e) {
																return $e['ID'] == $GLOBALS['arItemFilter']['COMPANY'];
															}))['NAME']); ?>"><?
																					echo reset(array_filter($arResult['COMPANIES'], function ($e) {
																						return $e['ID'] == $GLOBALS['arItemFilter']['COMPANY'];
																					}))['NAME'];
																					unset($GLOBALS['arItemFilter']); ?>
						</td>
						<!-- <td class=" fix-150" title="<? //= strip_tags($arResult['TYPE_NAME'][$arItem['TYPE']]); 
																	?>"><? //= strip_tags($arResult['TYPE_NAME'][$arItem['TYPE']]); 
																																						?></td> -->
						<td class="fix-150" title="<?= number_format((float)$arItem['PRICE'], 0, '', ' ') . "₽"; ?>"><?= number_format((float)$arItem['PRICE'], 0, '', ' ') . "₽"; ?></td>
						<td class="fix-150" title="<?= $arResult['IBLOCK_NAME'][$arItem['IBLOCK_ID']] ?>"><?= $arResult['IBLOCK_NAME'][$arItem['IBLOCK_ID']] ?></td>
						<td></td>
					</tr>
				<? endforeach; ?>
			</tbody>
		</table>

	</div>
</div>

<div class="container container-small">
	<div class="bottom-panel flex jc-sb ai-center small">

		<div id="ajaxUpdatePagenavigation" style="display: contents;">
			<? $APPLICATION->IncludeComponent(
				"bitrix:main.pagenavigation",
				$arParams['PAGER_TEMPLATE'],
				array(
					"NAV_OBJECT" => $arResult['NAV_OBJ'],
					"SEF_MODE" => "N",
				),
				false
			); ?>
			<p></p>
		</div>

		<div class="show flex ai-center">
			<p><?= GetMessage('BTN_VIEW') ?></p>

			<form>
				<div class="__select __select-small __select-count-js" data-state="">
					<div class="__select__title __select__title-small __select-count-title-js">
						<?= $_GET['count'] ? $_GET['count'] : $arParams['NEWS_COUNT'] ?>
					</div>
					<div class="__select__content __select__content-small">
						<input id="singleSelect1" class="__select__input" type="radio" name="singleSelect" />
						<label for="singleSelect1" class="__select__label __select__label-small __select-count-label-js"><?= $arParams['NEWS_START_COUNT'] ?></label>
						<input id="singleSelect2" class="__select__input" type="radio" name="singleSelect" />
						<label for="singleSelect2" class="__select__label __select__label-small __select-count-label-js"><?= $arParams['NEWS_START_COUNT'] * 2 ?></label>
						<input id="singleSelect3" class="__select__input" type="radio" name="singleSelect" />
						<label for="singleSelect3" class="__select__label __select__label-small __select-count-label-js"><?= $arParams['NEWS_START_COUNT'] * 4 ?></label>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="selected flex small hidden">
		<div class="selected-text--js"><?= GetMessage('ELEMENTS_SELECTED') ?></div>
		<button class="highlited"><?= GetMessage('ELEMENTS_SHOW_ONLY_SELECTED') ?></button>
		<button class="reset reset--js"><?= GetMessage('ELEMENTS_DROP_ALL') ?></button>
	</div>
</div>

<?
// передача всех параметров в js в зашифрованном виде
$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedParams = $signer->sign(base64_encode(serialize($arParams)), $templateName); ?>
<script type="text/javascript">
	BX.ComponentEx = {
		signedParameters: '<?= $signedParams ?>',
		componentName: '<?= $this->getComponent()->getName() ?>',
		templateName: '<?= $templateName ?>',
		ajaxPath: '<?= $this->GetFolder() . '/ajax.php' ?>',
	}
	BX.ComponentComments = {
		ajaxPath: '<?= $this->GetFolder() . '/ajax_comments.php' ?>',
	}
</script>