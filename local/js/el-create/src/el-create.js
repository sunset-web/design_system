/**
 * теперь этот файл/поток будет кодироваться в UTF-8
 */
import { BitrixVue } from "ui.vue3";
import { DateInput } from "./DateInput";
import { ErrModal } from "./ErrModal";
import { SuccessModalEdit } from "./SuccessModalEdit";
import { SuccessModalAdd } from "./SuccessModalAdd";
import { SuccessModalDelete } from "./SuccessModalDelete";
import { ErrorModal } from "./ErrorModal";
import { DeleteModal } from "./DeleteModal";
import "./fade.css";

export class ElementCreate {
	#app;

	constructor(rootNode) {
		this.rootNode = document.querySelector(rootNode);
	}

	init() {
		this.#app = BitrixVue.createApp({
			name: "ElementCreate",
			components: {
				DateInput,
				ErrModal,
				SuccessModalEdit,
				SuccessModalAdd,
				SuccessModalDelete,
				ErrorModal,
				DeleteModal,
			},
			data() {
				return {
					arResult: {},
					cancelCount: 0,
					FIELDS: {},
					OPTIONS: {},
					ERR: {},

					SuccessAdd: false,
					SuccessEdit: false,
					SuccessDelete: false,

					componentName: "",
					scriptCast: "",
					componentId: "",

					IsDeleteModal: false,
					IsErrorModal: false,

					fade: 0,

					backURL: "",
					backID: "",
				};
			},
			mounted() {
				this.fade++;
				BX.ajax({
					url: El_Create_Script_Path,
					data: {
						el_id: El_Create_Vue_Id,
						link_add: typeof Link_add != "undefined" ? Link_add : [""],
					},
					method: "POST",
					dataType: "html", // html|json|script – данные какого типа предполагаются в ответе
					timeout: 30,
					async: true,
					processData: true, // нужно ли сразу обрабатывать данные?
					scriptsRunFirst: true, // нужно ли выполнять все найденные скрипты перед тем, как отдавать сожеримое обработчику или только те, в тэге которых присутствует атрибут bxrunfirst
					emulateOnload: true, // нужно ли эмулировать событие window.onload для загруженных скриптов
					start: true, // отправить ли запрос сразу или он будет запущен вручную
					cache: false, // в случае значения false к параметру URL будет добавляться случайный кусок, чтобы избежать браузерного кэширования
					onsuccess: (response) => {
						let res = JSON.parse(response);
						this.arResult = res.arResult;
						this.backID = typeof El_back_Id != "undefined" ? El_back_Id : "";
						this.componentName = res.componentName;
						this.componentId = res.componentId;
						this.scriptCast = res.scriptCast;
						if (El_Create_Vue_Id) {
							this.FIELDS = JSON.parse(
								JSON.stringify(this.arResult.ELEMENT.FIELDS)
							);
							this.OPTIONS = JSON.parse(
								JSON.stringify(this.arResult.ELEMENT.OPTIONS)
							);
						}
					},
					onfailure: (response) => {
						console.error(response);
					},
				});

				// * фикс стоимости
				// ФОРМАТИРОВАНИЕ ЧИСЛА (РАЗДЕЛЕНИЕ НА РАЗРЯДЫ)
				const formatFIeldWithRoubles = (field) => {
					let val = field.val().replace(/[^0-9.]/g, "");
					if (val.length === 0) {
						field.val("");
						return;
					}

					if (val.indexOf(".") != "-1") {
						val = val.substring(0, val.indexOf(".") + 3);
					}

					val = val.replace(/\B(?=(\d{3})+(?!\d))/g, " ");

					field.val(val + " ₽");
				};

				$(".format-number-field--js").each(function () {
					formatFIeldWithRoubles($(this));
				});

				$.fn.setCursorPosition = function (pos) {
					this.each(function (index, elem) {
						if (elem.setSelectionRange) {
							elem.setSelectionRange(pos, pos);
						} else if (elem.createTextRange) {
							var range = elem.createTextRange();
							range.collapse(true);
							range.moveEnd("character", pos);
							range.moveStart("character", pos);
							range.select();
						}
					});
					return this;
				};

				$(document).on(
					"input",
					".format-number-field--js",
					function ({ target }) {
						formatFIeldWithRoubles($(target));
						$(target).setCursorPosition($(target).val().length - 2);
					}
				);
				// КОНЕЦ ФОРМАТИРОВАНИЕ ЧИСЛА (РАЗДЕЛЕНИЕ НА РАЗРЯДЫ)
			},
			methods: {
				SuccessAddDone() {
					this.SuccessAdd = false;
					window.location.href = this.backURL ? this.backURL : window.location.href.replace("-create", "");
				},
				SuccessEditDone() {
					this.SuccessEdit = false;
				},
				SuccessDeleteDone() {
					this.SuccessDelete = false;
					window.location.href = window.location.href.replace(
						`${El_Create_Vue_Id}/`,
						""
					);
				},
				errorDone() {
					this.ERR = {};
				},
				isEmpty(obj) {
					for (let key in obj) {
						return false;
					}
					return true;
				},
				bxEdit() {
					this.ERR = {};
					for (const key in this.arResult.FIELDS) {
						if (Object.hasOwnProperty.call(this.arResult.FIELDS, key)) {
							const field = this.arResult.FIELDS[key];
							if (field.REQUIRED && !this.FIELDS[key]) {
								this.ERR[key] = field;
							}
						}
					}
					for (const key in this.arResult.OPTIONS) {
						if (Object.hasOwnProperty.call(this.arResult.OPTIONS, key)) {
							const field = this.arResult.OPTIONS[key];
							if (field.REQUIRED && !this.OPTIONS[key]) {
								this.ERR[key] = field;
							}
						}
					}
					for (const key in this.arResult.DATES) {
						if (Object.hasOwnProperty.call(this.arResult.DATES, key)) {
							const field = this.arResult.DATES[key];
							if (field.REQUIRED && !this.FIELDS[key]) {
								this.ERR[key] = field;
							}
						}
					}
					if (!this.isEmpty(this.ERR)) {
					} else {
						BX.ajax
							.runComponentAction(this.componentName, "edit", {
								mode: "class",
								data: {
									// переменные для получения параметров
									componentId: this.componentId,
									scriptCast: this.scriptCast,

									elementId: this.arResult.elementId,
									options: this.OPTIONS,
									fields: this.FIELDS,
								},
							})
							.then(
								(response) => {
									// обработка ответа
									if (response.data.result["SUCCESS"]) {
										this.SuccessEdit = true;
									} else if (response.data.result["ERROR"]) {
										let allFields = Object.assign(
											this.arResult.FIELDS,
											this.arResult.OPTIONS
										);
										response.data.result["ERROR"].forEach((code) => {
											this.ERR[code] = allFields[code];
										});
									}
								},
								(res) => {
									this.IsErrorModal = true;
								}
							);
					}
				},
				bxCreate() {
					this.ERR = {};
					for (const key in this.arResult.FIELDS) {
						if (Object.hasOwnProperty.call(this.arResult.FIELDS, key)) {
							const field = this.arResult.FIELDS[key];
							if (field.REQUIRED && !this.FIELDS[key]) {
								this.ERR[key] = field;
							}
						}
					}
					for (const key in this.arResult.OPTIONS) {
						if (Object.hasOwnProperty.call(this.arResult.OPTIONS, key)) {
							const field = this.arResult.OPTIONS[key];
							if (field.REQUIRED && !this.OPTIONS[key]) {
								this.ERR[key] = field;
							}
						}
					}
					for (const key in this.arResult.DATES) {
						if (Object.hasOwnProperty.call(this.arResult.DATES, key)) {
							const field = this.arResult.DATES[key];
							if (field.REQUIRED && !this.FIELDS[key]) {
								this.ERR[key] = field;
							}
						}
					}
					if (!this.isEmpty(this.ERR)) {
					} else {
						BX.ajax
							.runComponentAction(this.componentName, "create", {
								mode: "class",
								data: {
									// переменные для получения параметров
									componentId: this.componentId,
									scriptCast: this.scriptCast,
									back: this.backID,
									options: this.OPTIONS,
									fields: this.FIELDS,
								},
							})
							.then(
								(response) => {
									// обработка ответа
									if (response.data.result["SUCCESS"]) {
										this.SuccessAdd = true;
										this.backURL = response.data.result["BACK_URL"] || "";
									} else if (response.data.result["ERROR"]) {
										let allFields = Object.assign(
											this.arResult.FIELDS,
											this.arResult.OPTIONS
										);
										response.data.result["ERROR"].forEach((code) => {
											this.ERR[code] = allFields[code];
										});
									}
								},
								(res) => {
									this.IsErrorModal = true;
								}
							);
					}
				},
				bxDelete() {
					BX.ajax
						.runComponentAction(this.componentName, "delete", {
							mode: "class",
							data: {
								elementId: El_Create_Vue_Id,
							},
						})
						.then(
							(response) => {
								// обработка ответа
								if (response.data.result) {
									this.SuccessDelete = true;
									this.IsDeleteModal = false;
								} else {
									this.IsDeleteModal = false;
									this.IsErrorModal = true;
								}
							},
							(response) => {
								this.IsDeleteModal = false;
								this.IsErrorModal = true;
							}
						);
				},
				cancel() {
					this.FIELDS = {};
					this.OPTIONS = {};
					this.cancelCount++;
				},
				cancelEdit() {
					this.FIELDS = JSON.parse(
						JSON.stringify(this.arResult.ELEMENT.FIELDS)
					);
					this.OPTIONS = JSON.parse(
						JSON.stringify(this.arResult.ELEMENT.OPTIONS)
					);

					this.cancelCount++;
				},
				onInputPrice(e, key) {
					// * фикс стоимости
					this.FIELDS[key] = e.target.value.replace(/[^.\d]/g, "") * 1;
				},
			},
			computed: {
				disabled() {
					let dis = true;
					for (const key in this.FIELDS) {
						if (Object.hasOwnProperty.call(this.FIELDS, key)) {
							const el = this.FIELDS[key];
							if (el != "") {
								dis = false;
							}
						}
					}
					for (const key in this.OPTIONS) {
						if (Object.hasOwnProperty.call(this.OPTIONS, key)) {
							const el = this.OPTIONS[key];
							if (el != "") {
								dis = false;
							}
						}
					}
					return dis;
				},
				disabledUpdate() {
					let dis = true;
					for (const key in this.FIELDS) {
						if (Object.hasOwnProperty.call(this.FIELDS, key)) {
							const el = this.FIELDS[key];
							if (el != this.arResult.ELEMENT.FIELDS[key]) {
								dis = false;
							}
						}
					}
					for (const key in this.OPTIONS) {
						if (Object.hasOwnProperty.call(this.OPTIONS, key)) {
							const el = this.OPTIONS[key];
							if (el != this.arResult.ELEMENT.OPTIONS[key]) {
								dis = false;
							}
						}
					}
					return dis;
				},
				disableBtnDelete() {
					return this.arResult.delBtn == "Y" && El_Create_Vue_Id != "";
				},
			},
			/* html */
			template: `
					<ErrModal @closeModalErr="errorDone" :ERR="ERR" v-if="!isEmpty(ERR)"></ErrModal>
					<SuccessModalAdd @closeModalSuccess="SuccessAddDone" v-if="SuccessAdd"></SuccessModalAdd>
					<SuccessModalEdit @closeModalSuccess="SuccessEditDone" v-if="SuccessEdit"></SuccessModalEdit>
					<SuccessModalDelete @closeModalSuccess="SuccessDeleteDone" v-if="SuccessDelete"></SuccessModalDelete>

					<DeleteModal @closeModal="IsDeleteModal = false" @deleteClick="bxDelete" v-if="IsDeleteModal"></DeleteModal>
					<ErrorModal @closeModal="IsErrorModal = false" v-if="IsErrorModal"></ErrorModal>

					<Transition>
					<div v-if="fade" >
						<form id="createForm" action="#" method="POST" class="tab-page current-tab-page form--js" data-page="info" :key="cancelCount">
							<div class="create-page__input-group flex fd-column">

								<template v-for="(field,key,index) in arResult.OPTIONS" key="index">
									<div class="form-input-field-group form-input-field-group--js" v-if="field.TYPE == 'STRING'">
											<input :data-name="field.CODE" type="text" :placeholder="field.NAME + (field.REQUIRED?' *':'')" :name="field.CODE" v-model="OPTIONS[key]">
											<label :for="field.CODE" v-html="field.NAME + (field.REQUIRED?' *':'')"></label>
									</div>
									<div class="form-input-field-group form-input-field-group--js" v-if="field.TYPE == 'HTML'">
											<textarea :data-name="field.CODE" :name="field.CODE" :placeholder="field.NAME + (field.REQUIRED?' *':'')" @input="OPTIONS[key] = $event.target.value" v-html="OPTIONS[key]"></textarea>
											<label :for="field.CODE" v-html="field.NAME + (field.REQUIRED?' *':'')"></label>
									</div>
								</template>

								<div class="datepickers-group flex fd-column-xs" v-if="arResult.DATES">
										<DateInput v-for="(field,key) in arResult.DATES" :key="key" :field="field" @input="FIELDS[key] = $event.target.value" :value="FIELDS[key]"></DateInput>
										<div class="help-icon help-icon--js">
										<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" clip-rule="evenodd" d="M5.5415 2.33417C7.45322 1.05679 9.7008 0.375 12 0.375C15.0831 0.375 18.04 1.59977 20.2201 3.77988C22.4002 5.95999 23.625 8.91686 23.625 12C23.625 14.2992 22.9432 16.5468 21.6658 18.4585C20.3885 20.3702 18.5729 21.8602 16.4487 22.7401C14.3245 23.62 11.9871 23.8502 9.73208 23.4016C7.47705 22.9531 5.40567 21.8459 3.77989 20.2201C2.1541 18.5943 1.04693 16.523 0.598376 14.2679C0.149823 12.0129 0.380037 9.6755 1.25991 7.55131C2.13977 5.42711 3.62978 3.61154 5.5415 2.33417ZM12 1.625C9.94802 1.625 7.94212 2.23348 6.23596 3.3735C4.5298 4.51352 3.20001 6.13388 2.41476 8.02966C1.6295 9.92544 1.42404 12.0115 1.82436 14.0241C2.22468 16.0366 3.2128 17.8853 4.66377 19.3362C6.11474 20.7872 7.96339 21.7753 9.97594 22.1756C11.9885 22.576 14.0746 22.3705 15.9703 21.5852C17.8661 20.8 19.4865 19.4702 20.6265 17.764C21.7665 16.0579 22.375 14.052 22.375 12C22.375 9.24838 21.2819 6.60946 19.3362 4.66377C17.3905 2.71808 14.7516 1.625 12 1.625Z" />
												<path fill-rule="evenodd" clip-rule="evenodd" d="M13.3842 6.65501C13.1508 6.54205 12.6497 6.43007 12.0098 6.43781C11.3316 6.44709 10.6616 6.59638 10.1241 7.03683M13.3842 6.65501C13.9874 6.94884 14.8664 7.52652 14.8664 8.68778C14.8664 9.96484 14.1228 10.538 12.8417 11.4139C12.1468 11.889 11.6583 12.3954 11.3503 12.9646C11.0396 13.5389 10.944 14.1194 10.944 14.6875C10.944 15.1017 11.2817 15.4375 11.6983 15.4375C12.1149 15.4375 12.4526 15.1017 12.4526 14.6875C12.4526 14.2942 12.5169 13.9745 12.6789 13.6752C12.8436 13.3708 13.1404 13.0301 13.6965 12.6498L13.7255 12.63C14.9404 11.7995 16.375 10.8188 16.375 8.68778C16.375 6.59463 14.7345 5.64237 14.0471 5.30762L14.0465 5.30732L14.0459 5.30702C13.5241 5.05411 12.7629 4.92857 11.9907 4.93799L11.9902 4.938L11.9897 4.93801C11.121 4.9498 10.0646 5.14178 9.16453 5.8794L9.16451 5.87942C8.4402 6.47303 8.05494 7.14713 7.8523 7.67966C7.75138 7.94487 7.69552 8.17532 7.66454 8.34499C7.64902 8.43 7.63965 8.50034 7.63399 8.55289C7.63116 8.57918 7.62926 8.60109 7.62798 8.61824C7.62734 8.62681 7.62686 8.6342 7.6265 8.64035C7.62632 8.64343 7.62616 8.6462 7.62604 8.64866L7.62587 8.65211L7.62579 8.65366L7.62576 8.65439C7.62575 8.65474 7.62573 8.65509 8.37932 8.68778L7.62573 8.65509C7.60757 9.06889 7.93024 9.41898 8.34644 9.43704C8.75998 9.45498 9.11025 9.13837 9.13253 8.7284C9.13269 8.72627 9.13314 8.72088 9.13405 8.71246C9.13609 8.69345 9.14042 8.65936 9.14891 8.61286C9.16595 8.5195 9.19933 8.37858 9.26332 8.21041C9.3905 7.87616 9.63772 7.43544 10.1241 7.03685" />
												<path d="M11.625 18.875C12.2463 18.875 12.75 18.3713 12.75 17.75C12.75 17.1287 12.2463 16.625 11.625 16.625C11.0037 16.625 10.5 17.1287 10.5 17.75C10.5 18.3713 11.0037 18.875 11.625 18.875Z" />
										</svg>
									</div>
									<div class="help-info small help-info--js">
									{{this.$Bitrix.Loc.getMessage('DATE_INFO')}}
									</div>
								</div>

								<template v-for="(field,key,index) in arResult.FIELDS" key="index">
									<div class="create-page__subdivisions" v-if="field.TYPE == 'SELECT'">
											<div class="__select __select_tariff __select-form-js ">
												<div class="__select__title __select_tariff__title" :style="FIELDS[key]?'color: rgb(27, 37, 74);':''" v-html="FIELDS[key]?field.VARIABLES[FIELDS[key]]:field.NAME + (field.REQUIRED?' *':'')"></div>
												<div class="select_placeholder" :class="{'visible':FIELDS[key]}" v-html="field.NAME + (field.REQUIRED?' *':'')"></div>
												<div class="__select__content __select_tariff__content">
													<div class="__select__content_scrolling">
														<input type="hidden" :data-name="field.CODE" :placeholder="field.NAME + (field.REQUIRED?' *':'')" v-model="FIELDS[key]">
														<label v-if="!field.REQUIRED && FIELDS[key]" class="__select__label __select_tariff__label" @click="delete FIELDS[key]">{{this.$Bitrix.Loc.getMessage('NOTHING')}}</label>
														<template v-for="(variable,key) in field.VARIABLES">
															<input @click="FIELDS[field.CODE] = key" :id="key" class="__select__input" type="radio" :name="key" :value="key" />
															<label :for="key" :data-val="key" class="__select__label __select_tariff__label" v-html="variable"></label>
														</template>
													</div>
													<template v-if="field.LINK_ADD">
														<a :href="'/personal/'+field.API_CODE+'-create/?back='+arResult.elementId" class="__select__create">{{this.$Bitrix.Loc.getMessage('NEW_ELEMENT')}}</a>
													</template>
												</div>
												
											</div>
									</div>
									<div class="form-input-field-group form-input-field-group--js" v-if="field.TYPE == 'INT'">
											<input :value="FIELDS[key]" @input="(e)=>{onInputPrice(e,key)}" :data-name="field.CODE" :class="{'format-number-field--js':field.CODE=='PRICE'}" type="text" :placeholder="field.NAME + (field.REQUIRED?' *':'')" :name="field.CODE">
											<label :for="field.CODE" v-html="field.NAME + (field.REQUIRED?' *':'')"></label>
									</div>
									<div class="form-input-field-group form-input-field-group--js" v-if="field.TYPE == 'STRING'">
											<input :data-name="field.CODE" type="text" :placeholder="field.NAME + (field.REQUIRED?' *':'')" :name="field.CODE" v-model="FIELDS[key]">
											<label :for="field.CODE" v-html="field.NAME + (field.REQUIRED?' *':'')"></label>
									</div>
									<div class="form-input-field-group form-input-field-group--js" v-if="field.TYPE == 'HTML'" >
											<textarea :data-name="field.CODE" :name="field.CODE" :placeholder="field.NAME + (field.REQUIRED?' *':'')" v-model="FIELDS[key]"></textarea>
											<label :for="field.CODE" v-html="field.NAME + (field.REQUIRED?' *':'')"></label>
									</div>
								</template>

							</div>

							<div class="create-page__button-group flex">
								<button v-if="arResult.elementId" @click.prevent="bxEdit" class="btn-blue p13-24" type="submit" :disabled="disabledUpdate">{{this.$Bitrix.Loc.getMessage('SAVE')}}</button>
								<button v-if="arResult.elementId" @click="cancelEdit" class="btn-white p13-24" type="button" :disabled="disabledUpdate">{{this.$Bitrix.Loc.getMessage('CANCEL')}}</button>
								<button v-if="!arResult.elementId" @click.prevent="bxCreate" class="btn-blue p13-24" type="submit" :disabled="disabled">{{this.$Bitrix.Loc.getMessage('ADD')}}</button>
								<button v-if="!arResult.elementId" @click="cancel" class="btn-white p13-24" type="button" :disabled="disabled">{{this.$Bitrix.Loc.getMessage('CANCEL')}}</button>
								<button v-if="disableBtnDelete" @click.prevent="IsDeleteModal = true" class="btn-delete" type="button">{{this.$Bitrix.Loc.getMessage('DELETE_BTN')}}</button>
							</div>

						</form>
					</div>
					</Transition>
			`,
		});
		this.#app.mount(this.rootNode);
	}
}
