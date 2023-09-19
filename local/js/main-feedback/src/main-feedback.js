import { BitrixVue } from "ui.vue3";

export const MainFeedback = BitrixVue.mutableComponent("MainFeedback", {
	data() {
		return {
			ArResult: BX.CallbackOrderResult,
			ArParams: BX.CallbackOrderParams,
			input_security: "",
			policy: true,
			name: "",
			phone: "",
			errors: [],
		};
	},
	mounted() {
		setTimeout(() => {
			this.input_security = "upfly";
		}, 4000);
	},
	methods: {
		sendForm(e) {
			if (this.policy) {
				BX.ajax
					.runComponentAction("upfly:main.feedback.new", "send", {
						mode: "class", //это означает, что мы хотим вызывать действие из class.php
						data: {
							data: {
								input_security: this.input_security,
								NAME: this.name,
								PHONE: this.phone,
							},
							arResultForm: this.ArResult,
							arParamsForm: this.ArParams,
						},
						analyticsLabel: {
							viewMode: "grid",
							filterState: "closed",
						},
					})
					.then((response) => {
						let res = response.data;
						if (res.err) {
							// вывод ошибок
							this.errors = res.err;
						} else {
							// успешно
							$(this.$refs.modal).fadeOut(300);
							setTimeout(() => {
								$(this.$refs.modal).html(
									$('[data-modal="application-thanks"]').html()
								);
							}, 300);
							$('[data-modal="application-thanks"]').fadeIn(300);
						}
					});
			}
		},
	},
	/*html*/
	template: `
<div ref="modal" class="modal modal--js" data-modal="callback-order">
	<div class="modal-wrap-scroll">
		<button class="close-btn close-modal--js">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
				<path d="M13.4868 11.8939L13.3807 12L13.4869 12.1061L17.9428 16.5592C17.9428 16.5592 17.9428 16.5592 17.9428 16.5592C18.126 16.7424 18.2289 16.9909 18.2289 17.25C18.2289 17.5091 18.126 17.7576 17.9428 17.9408C17.7595 18.124 17.5111 18.2269 17.2519 18.2269C16.9928 18.2269 16.7444 18.124 16.5611 17.9408L12.108 13.4877L12.002 13.3816L11.8959 13.4877L7.44276 17.9408C7.25955 18.124 7.01106 18.2269 6.75195 18.2269C6.49285 18.2269 6.24436 18.124 6.06114 17.9408C5.87793 17.7576 5.775 17.5091 5.775 17.25C5.775 16.9909 5.87793 16.7424 6.06114 16.5592L10.5143 12.1061L10.6203 12L10.5143 11.8939L6.06114 7.44081C5.87793 7.25759 5.775 7.0091 5.775 6.75C5.775 6.49089 5.87793 6.2424 6.06114 6.05919C6.24436 5.87598 6.49285 5.77305 6.75195 5.77305C7.01105 5.77305 7.25955 5.87598 7.44276 6.05919L11.8959 10.5123L12.002 10.6184L12.108 10.5123L16.561 6.05928C16.7442 5.87642 16.9926 5.77381 17.2514 5.77404C17.5102 5.77427 17.7584 5.87731 17.9413 6.0605L18.0468 5.95519L17.9413 6.0605C18.1241 6.24369 18.2267 6.49202 18.2265 6.75086C18.2263 7.0097 18.1232 7.25785 17.94 7.44071L17.9399 7.44081L13.4868 11.8939Z" stroke="white" stroke-width="0.3" />
			</svg>
		</button>

			<h2 v-if="ArResult['TITLE_FORM']" class="mb-24-fixed tal not-scroll--js" style="margin-bottom:0;">{{ArResult["TITLE_FORM"]}}</h2>
			<span v-if="(errors && errors.SECURITY_TEXT)" class="error-text mb-24-fixed">{{errors.SECURITY_TEXT}}</span>


		<!-- блок с обычным выпадающим списком, в загловок записывается выбранный пункт, закрывается по клику на пункт или по клику мимо -->
		<form method="POST" novalidate="novalidate" id="modal-form" @submit.prevent="sendForm">

			<input type="hidden" name="user_title" id="title_form_submit_down" value="">
			<input type="hidden" name="user_security" class="input_security" :value="input_security">
			<input type="hidden" name="PARAMS_HASH" :value="ArResult['PARAMS_HASH']">

			<div class="scroll-part-pseudo">

				<div class="p-r">
					<input :class="{'input-with-error':(errors && errors.NAME)}" type="text" name="NAME" id="NAME" required data-validate-field="NAME" v-model="name" :placeholder="$Bitrix.Loc.getMessage('MFT_NAME')">
					<span v-if="(errors && errors.NAME)" class="error-text">{{errors.NAME}}</span>
				</div>

				<div class="p-r">
					<input :class="{'input-with-error':(errors && errors.PHONE)}" type="tel" name="PHONE" id="PHONE" required data-validate-field="PHONE" v-model="phone" :placeholder="$Bitrix.Loc.getMessage('MFT_PHONE')" class="phone-input--js">
					<span v-if="(errors && errors.PHONE)" class="error-text">{{errors.PHONE}}</span>
				</div>

				<div class="note tal mb-24-fixed">
					{{$Bitrix.Loc.getMessage('DESCRIPTION_BLOCK')}}
				</div>
			</div>

			<div class="not-scroll--js">
				<button :disabled="!this.policy" name="submit" value="submit" type="submit" class="green w-216 m-0-a mb-16-fixed">{{$Bitrix.Loc.getMessage('MFT_SUBMIT')}}</button>

				<div class="checkbox_wrapper" v-if="ArResult['POLICY_TEXT']">
					<label class="custom-checkbox option-label">
						<input type="checkbox" required checked v-model="policy">

							<span class="checkbox_content"><span v-html="ArResult['POLICY_TEXT']"></span></span>

					</label>
				</div>
			</div>
		</form>
	</div>
</div>
`,
});
