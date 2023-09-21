/**
 * теперь этот файл/поток будет кодироваться в UTF-8
 */
import { BitrixVue } from "ui.vue3";
import { SuccessModal } from "./SuccessModal";
import { ErrorModal } from "./ErrorModal";
import "./fade.css";
import "./security.css";

export class ChangePass {
	#app;

	constructor(rootNode) {
		this.rootNode = document.querySelector(rootNode);
	}

	init() {
		this.#app = BitrixVue.createApp({
			name: "ChangePass",
			components: { SuccessModal, ErrorModal },
			data() {
				return {
					Success: false,
					Error: false,
					fade: 0,
					result: {},

					password: "",
					confirm_password: "",
					old_password: "",
				};
			},
			mounted() {
				this.fade++;
			},
			methods: {
				SuccessDone() {
					this.Success = false;
				},
				ErrorDone() {
					this.Error = false;
				},
				save() {
					BX.ajax({
						url: "https://itin.online/local/api/change.pass/default.php",
						data: {
							password: this.password,
							confirm_password: this.confirm_password,
							old_password: this.old_password,
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
							if (res.SUCCESS == "Y") {
								this.Success = true;
								this.disableBtns = true;
							} else {
								this.Error = res.ERROR;
							}
						},
						onfailure: (err) => {
							console.error(err);
						},
					});
				},
			},
			computed: {
				disableBtns() {
					if (
						this.password !== "" &&
						this.confirm_password !== "" &&
						this.old_password !== ""
					) {
						return false;
					} else {
						return true;
					}
				},
			},
			/* html */
			template: `
					<Transition>
						<SuccessModal @closeModal="SuccessDone" v-if="Success"></SuccessModal>
					</Transition>

					<Transition>
						<ErrorModal :ERROR="this.Error" @closeModal="ErrorDone" v-if="Error"></ErrorModal>
					</Transition>

					<Transition>
						<div v-if="fade" >
							<form class="security-form security-form--js form--js">

								<div class="security-inputs-group flex fd-column">
									<div class="form-input-field-group form-input-field-group--js">
										<input type="password" :placeholder="this.$Bitrix.Loc.getMessage('MAIN_AUTH_CHD_FIELD_PASS3')" name="old_password" v-model="old_password" >
										<label class="js-label-password" for="old_password">{{this.$Bitrix.Loc.getMessage('MAIN_AUTH_CHD_FIELD_PASS3')}}</label>
										<button type="button" class="eye-button eye-button--js"></button>
									</div>

									<div class="form-input-field-group form-input-field-group--js">
										<input type="password" :placeholder="this.$Bitrix.Loc.getMessage('MAIN_AUTH_CHD_FIELD_PASS')" name="password" v-model="password" >
										<label class="js-label-password" for="password">{{this.$Bitrix.Loc.getMessage('MAIN_AUTH_CHD_FIELD_PASS')}}</label>
										<button type="button" class="eye-button eye-button--js"></button>
									</div>

									<div class="form-input-field-group form-input-field-group--js">
										<input type="password" :placeholder="this.$Bitrix.Loc.getMessage('MAIN_AUTH_CHD_FIELD_PASS2')" name="confirm_password" v-model="confirm_password" >
										<label class="js-label-password" for="confirm_password">{{this.$Bitrix.Loc.getMessage('MAIN_AUTH_CHD_FIELD_PASS2')}}</label>
										<button type="button" class="eye-button eye-button--js"></button>
									</div>
								</div>

								<button @click="save" class="security-btn btn-blue p13-24 sign-in-btn" type="submit" :disabled="disableBtns">{{this.$Bitrix.Loc.getMessage('MAIN_AUTH_CHD_FIELD_SUBMIT')}}</button>

							</form>
						</div>
					</Transition>
			`,
		});
		this.#app.mount(this.rootNode);
	}
}
// {{this.$Bitrix.Loc.getMessage('DELETE_BTN')}}
