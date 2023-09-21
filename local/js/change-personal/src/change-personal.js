/**
 * теперь этот файл/поток будет кодироваться в UTF-8
 */
import { BitrixVue } from "ui.vue3";
import { ErrorModal } from "./ErrorModal";
import { SuccessModal } from "./SuccessModal";
import "./fade.css";
import "./profile.css";

export class ChangePersonal {
	#app;

	constructor(rootNode) {
		this.rootNode = document.querySelector(rootNode);
	}

	init() {
		this.#app = BitrixVue.createApp({
			name: "ChangePersonal",
			components: {
				ErrorModal,
				SuccessModal,
			},
			data() {
				return {
					RequiredFields: ["NAME", "LAST_NAME", "EMAIL", "PERSONAL_PHONE"],
					Fields: {},
					defaults: {},

					cancelCount: 0,

					Error: false,
					Success: false,

					fade: 0,
				};
			},
			mounted() {
				this.fade++;
				//
				// https://dev.1c-bitrix.ru/api_help/js_lib/ajax/bx_ajax.php
				BX.ajax({
					url: "/local/api/change.personal/default.php",
					data: {
						action: "get",
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
					onsuccess: (data) => {
						let res = JSON.parse(data);

						this.Fields.LAST_NAME = res.LAST_NAME;
						this.Fields.NAME = res.NAME;
						this.Fields.SECOND_NAME = res.SECOND_NAME;
						this.Fields.EMAIL = res.EMAIL;
						this.Fields.PERSONAL_PHONE = res.PERSONAL_PHONE;

						this.defaults = JSON.parse(JSON.stringify(this.Fields));
					},
					onfailure: (data) => {
						console.log(data);
					},
				});
			},
			methods: {
				SuccessDone() {
					this.Success = false;
				},
				ErrorDone() {
					this.Error = false;
				},
				cancel() {
					this.Fields = JSON.parse(JSON.stringify(this.defaults));
					this.cancelCount++;
				},
				save() {
					let err = [];
					this.RequiredFields.forEach((code) => {
						if (this.Fields[code] == "") {
							err.push(code);
						}
					});
					if (err.length > 0) {
						this.Error = "";
						err.forEach((code) => {
							let name = $(`input[name="${code}"]`).attr("placeholder");
							let str = "Не заполнено обязательное поле " + name + "<br>";
							if (name) {
								this.Error += str;
							}
						});
					} else {
						BX.ajax({
							url: "https://itin.online/local/api/change.personal/default.php",
							data: {
								action: "update",
								fields: {
									LAST_NAME: this.Fields.LAST_NAME,
									NAME: this.Fields.NAME,
									SECOND_NAME: this.Fields.SECOND_NAME,
									EMAIL: this.Fields.EMAIL,
									PHONE_NUMBER: this.Fields.PERSONAL_PHONE,
								},
							},
							method: "POST",
							timeout: 30,
							async: true,
							processData: true,
							scriptsRunFirst: true,
							emulateOnload: true,
							start: true,
							cache: false,
							onsuccess: (data) => {
								// обработка ответа
								let res = JSON.parse(data);
								console.log(res);
								if (res.SUCCESS) {
									this.Success = true;
									this.defaults = JSON.parse(JSON.stringify(this.Fields));
									$(".header__profile--js .user_name").html(
										this.Fields.NAME + " " + this.Fields.LAST_NAME
									);
								} else {
									this.Error = res.ERROR;
								}
							},
							onfailure: (err) => {
								console.error(err);
							},
						});
					}
				},
			},
			computed: {
				disabled() {
					let dis = true;
					for (const key in this.Fields) {
						if (Object.hasOwnProperty.call(this.Fields, key)) {
							if (this.Fields[key] != this.defaults[key]) {
								dis = false;
							}
						}
					}
					return dis;
				},
			},
			// watch: {
			// 	Fields: {
			// 		handler(val) {
			// 			// console.log(val);
			// 		},
			// 		deep: true,
			// 	},
			// },
			/* html */
			template: `
					<Transition>
						<ErrorModal @closeModal="ErrorDone" :ERROR="Error" v-if="Error"></ErrorModal>
					</Transition>

					<Transition>
						<SuccessModal @closeModal="SuccessDone" v-if="Success"></SuccessModal>
					</Transition>

					<Transition>
						<div v-if="fade" >
							<form class="profile__form--js" :key="cancelCount">
								<div class="profile__input-group flex fd-column">
									<div class="form-input-field-group form-input-field-group--js">
										<input type="text" :placeholder="this.$Bitrix.Loc.getMessage('LAST_NAME')" name="LAST_NAME" v-model="Fields.LAST_NAME">
										<label for="last-name">{{this.$Bitrix.Loc.getMessage('LAST_NAME')}}</label>
									</div>

									<div class="form-input-field-group form-input-field-group--js">
										<input type="text" :placeholder="this.$Bitrix.Loc.getMessage('NAME')" name="NAME" v-model="Fields.NAME">
										<label for="first-name">{{this.$Bitrix.Loc.getMessage('NAME')}}</label>
									</div>

									<div class="form-input-field-group form-input-field-group--js">
										<input type="text" :placeholder="this.$Bitrix.Loc.getMessage('SECOND_NAME')" name="SECOND_NAME" v-model="Fields.SECOND_NAME">
										<label for="middle-name">{{this.$Bitrix.Loc.getMessage('SECOND_NAME')}}</label>
									</div>

									<div class="form-input-field-group form-input-field-group--js">
										<input type="text" :placeholder="this.$Bitrix.Loc.getMessage('EMAIL')" name="EMAIL" v-model="Fields.EMAIL">
										<label for="email">{{this.$Bitrix.Loc.getMessage('EMAIL')}}</label>
									</div>

									<div class="form-input-field-group form-input-field-group--js">
										<input ref="phone" type="text" :placeholder="this.$Bitrix.Loc.getMessage('USER_PHONE')" name="PERSONAL_PHONE" v-model="Fields.PERSONAL_PHONE" class="phone-input--js" maxlength="18">
										<label for="phone">{{this.$Bitrix.Loc.getMessage('USER_PHONE')}}</label>
									</div>
								</div>

								<div class="profile__button-group flex">
									<button @click.prevent="save" class="btn-blue p13-24" :disabled="disabled">{{this.$Bitrix.Loc.getMessage('SAVE_BTN')}}</button>
									<button @click.prevent="cancel" class="btn-white p13-24" :disabled="disabled">{{this.$Bitrix.Loc.getMessage('MAIN_RESET')}}</button>
								</div>
							</form>
						</div>
					</Transition>
			`,
		});
		this.#app.mount(this.rootNode);
	}
}
